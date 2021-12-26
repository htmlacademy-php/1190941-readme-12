<?php
/**
 * @var $db
 * @var int $isAuth
 * @var string $userName
 * @var array $userData
 * @var TransportInterface $transport
 * @var Email $message
 * @var Email $message
 */

use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'bootstrap.php';
require 'model/types.php';
require 'model/posts.php';
require 'model/hashtags.php';

$queryString = $_GET ?? null;
$postType = $queryString['type'] ?? null;
$subscribers = getSubscribers($db, $_SESSION['id']);

$formData = $_POST ?? null;
$formDataPostType = $formData['post-type'] ?? null;
$postTypes = getPostTypes($db);

$postType = current(array_filter($postTypes, function ($element) use ($postType) {
    return $element['class_name'] === $postType;
}));

if (!$postType) {
    header('Location: /add.php?type=text');
}

$noValidateFields = [
    'post-type',
    'photo-main',
    'photo-url',
];

$errors = [];

$fieldsMap = [
    "{$formDataPostType}-heading" => 'Заголовок',
    "{$formDataPostType}-main" => 'Основное содержимое',
    "{$formDataPostType}-tags" => 'Теги записи',
];

if (!empty($formData)) {
    foreach ($formData as $name => $value) {
        if (!in_array($name, $noValidateFields) && empty($value) && $name !== "{$formDataPostType}-tags") {
            $errors[$name]['name'] = $fieldsMap[$name] ?? null;
            $errors[$name]['title'] = 'Поле не заполнено';
            $errors[$name]['description'] = 'Это поле должно быть заполнено';
        }
    }

    if (!empty($formData[$formDataPostType . '-tags'])) {
        $tagsField = $formData[$formDataPostType . '-tags'];
        $tagsArray = explode(' ', $tagsField);

        foreach ($tagsArray as $tag) {
            if (mb_strlen($tag) > 255) {
                $errors[$formDataPostType . '-tags']['name'] = $fieldsMap[$formDataPostType . '-tags'];
                $errors[$formDataPostType . '-tags']['title'] = 'Один или больше тегов содержат 255+ символов';
                $errors[$formDataPostType . '-tags']['description'] = 'Один тег не может быть больше 255 символов';
            }
        }
    }

    $isFile = $_FILES['photo-main'] ?? null;

    if ($isFile && $isFile['error'] === 0) {
        $fileTempName = $_FILES['photo-main']['tmp_name'];
        $mimeType = mime_content_type($fileTempName);
        $acceptedMimeTypes = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];

        if (!in_array($mimeType, $acceptedMimeTypes)) {
            $errors['photo-main']['name'] = $fieldsMap['photo-main'];
            $errors['photo-main']['title'] = 'Не верный формат изображения';
            $errors['photo-main']['description'] = 'Пожалуйста загрузите фотографию в одном из форматов - png, jpeg, gif';
        }

    } elseif (isset($formData['photo-url'])) {

        if (empty($formData['photo-url'])) {

            $errors['photo-main']['name'] = $fieldsMap['photo-main'];
            $errors['photo-main']['title'] = 'Заполните одно из полей';
            $errors['photo-main']['description'] = 'Укажите ссылку на источник фотографии или добавьте свое фото';

        } elseif (!filter_var($formData['photo-url'], FILTER_VALIDATE_URL)) {

            $errors['photo-main']['name'] = $fieldsMap['photo-main'];
            $errors['photo-main']['title'] = 'Не корректный URL-адрес';
            $errors['photo-main']['description'] = 'Пожалуйста укажите корректный URL-адрес';

        } elseif (!file_get_contents($formData['photo-url'])) {

            $errors['photo-main']['name'] = $fieldsMap['photo-main'];
            $errors['photo-main']['title'] = 'Не удалось получить доступ к изображению';
            $errors['photo-main']['description'] = 'Пожалуйста проверьте корректен ли адрес';
        }
    }

    if ($formDataPostType === 'link' || $formDataPostType === 'video') {
        if (!empty($formData["{$formDataPostType}-main"]
            && !filter_var($formData["{$formDataPostType}-main"], FILTER_VALIDATE_URL))
        ) {
            $errors["{$formDataPostType}-main"]['name'] = $fieldsMap["{$formDataPostType}-main"];
            $errors["{$formDataPostType}-main"]['title'] = 'Не корректный URL-адрес';
            $errors["{$formDataPostType}-main"]['description'] = 'Пожалуйста укажите корректный URL-адрес';
        } elseif ($formDataPostType === 'video' && !checkYoutubeUrl($formData["{$formDataPostType}-main"])) {

            $errors["{$formDataPostType}-main"]['name'] = $fieldsMap["{$formDataPostType}-main"];
            $errors["{$formDataPostType}-main"]['title'] = 'По указанному адресу не найдено видео на Youtube';
            $errors["{$formDataPostType}-main"]['description'] = 'Пожалуйста укажите корректный URL-адрес';
        }
    }

    if (empty($errors)) {
        $typeID = current(array_filter($postTypes, function ($type) use ($formDataPostType) {
            return $type['class_name'] === $formDataPostType;
        }))['id'];

        $content = $formData["{$formDataPostType}-main"] ?? null;

        if (isset($isFile) && $isFile['error'] === 0) {
            $fileName = $_FILES['photo-main']['name'];
            $filePath = __DIR__ . '/uploads/photos/';
            $fileUrl = '/uploads/photos/' . $fileName;

            move_uploaded_file($_FILES['photo-main']['tmp_name'], $filePath . $fileName);

            $content = $fileName;
        } elseif (isset($formData['photo-url']) && $formDataPostType === 'photo') {

            $fileName = uniqid() . '.jpg';
            $filePath = 'uploads' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR;
            $fileContent = file_get_contents($formData['photo-url']);
            file_put_contents($filePath . $fileName, $fileContent);

            $content = $fileName;
        }

        insertNewPost(
            $db,
            $formData["{$formDataPostType}-heading"],
            $typeID,
            $_SESSION['id'],
            $content,
            $formDataPostType === 'quote' ? $formData['quote-author'] : null
        );
        $postId = $db->insert_id;

        if ($_POST["{$formDataPostType}-tags"]) {
            $tags = explode(' ', $_POST["{$formDataPostType}-tags"]);

            foreach ($tags as $tag) {
                $tagId = null;

                if (!selectTag($db, $tag)) {
                    insertTag($db, $tag);
                    $tagId = $db->insert_id;
                } else {
                    $tagId = selectTag($db, $tag)['id'];
                }

                if (!selectTagToPost($db, $tagId, $postId)) {
                    setTagToPost($db, $tagId, $postId);
                }
            }
        }

        foreach ($subscribers as $subscriber) {
            // Формирование сообщения
            $message = new Email();
            $message->to($subscriber['email']);
            $message->from("mail@readme.me");
            $message->subject("Новая публикация от пользователя {$userData['name']}");
            $message->text("Здравствуйте, {$subscriber['name']}. Пользователь {$userData['name']} только что опубликовал новую запись „{$formData["{$formDataPostType}-heading"]}“. Посмотрите её на странице пользователя: <a href=\"http://readme.cloc/profile.php?id={$_SESSION['id']}\">{$userData['name']}</a>");

            // Отправка сообщения
            $mailer = new Mailer($transport);
            $mailer->send($message);
        }

        header("Location: /post.php?id={$postId}");
    }
}

$pageMainContent = includeTemplate('add.php', [
    'postTypes' => $postTypes,
    'postType' => $postType,
    'errors' => $errors,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - Добавить пост',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'adding-post',
]);

print($pageLayout);
