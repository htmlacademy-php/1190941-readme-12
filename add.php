<?php
/**
 * @var $db
 * @var int $isAuth
 * @var string $userName
 * @var array $userData
 */

require 'bootstrap.php';
require 'model/types.php';
require 'model/posts.php';
require 'model/hashtags.php';

$formData = $_POST ?? null;
$postType = $formData['post-type'] ?? null;
$postTypes = getPostTypes($db);

$noValidateFields = [
    'post-type',
    'photo-main',
    'photo-url',
];
$errors = [];

$fieldsMap = [
    "{$postType}-heading" => 'Заголовок',
    "{$postType}-main" => 'Основное содержимое',
    "{$postType}-tags" => 'Теги записи',
];

if (!empty($formData)) {
    foreach ($formData as $name => $value) {
        if (!in_array($name, $noValidateFields) && empty($value)) {
            $errors[$name]['name'] = $fieldsMap[$name] ?? null;
            $errors[$name]['title'] = 'Поле не заполнено';
            $errors[$name]['description'] = 'Это поле должно быть заполнено';
        }
    }

    if (!empty($formData[$postType . '-tags'])) {
        $tagsField = $formData[$postType . '-tags'];
        $tagsArray = explode(' ', $tagsField);

        foreach ($tagsArray as $tag) {
            if (mb_strlen($tag) > 255) {
                $errors[$postType . '-tags']['name'] = $fieldsMap[$postType . '-tags'];
                $errors[$postType . '-tags']['title'] = 'Один или больше тегов содержат 255+ символов';
                $errors[$postType . '-tags']['description'] = 'Один тег не может быть больше 255 символов';
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

    if ($postType === 'link' || $postType === 'video') {
        if (!empty($formData["{$postType}-main"]
            && !filter_var($formData["{$postType}-main"], FILTER_VALIDATE_URL))) {

            $errors["{$postType}-main"]['name'] = $fieldsMap["{$postType}-main"];
            $errors["{$postType}-main"]['title'] = 'Не корректный URL-адрес';
            $errors["{$postType}-main"]['description'] = 'Пожалуйста укажите корректный URL-адрес';
        } elseif ($postType === 'video' && !checkYoutubeUrl($formData["{$postType}-main"])) {

            $errors["{$postType}-main"]['name'] = $fieldsMap["{$postType}-main"];
            $errors["{$postType}-main"]['title'] = 'По указанному адресу не найдено видео на Youtube';
            $errors["{$postType}-main"]['description'] = 'Пожалуйста укажите корректный URL-адрес';
        }
    }

    if (empty($errors)) {

        $data['title'] = $formData["{$postType}-heading"];
        $data['tags'] = explode(' ', $_POST["{$postType}-tags"]);
        $data['typeId'] = current(array_filter($postTypes, function ($type) use ($postType)
        {
            return $type['class_name'] === $postType;
        }))['id'];

        $data['authorId'] = $_SESSION['id'];
        $data['content'] = $formData["{$postType}-main"] ?? null;
        $data['citeAuthor'] = $postType === 'quote' ? $formData['quote-author'] : null;

        if (isset($isFile) && $isFile['error'] === 0) {
            // FIXME сгенерировать имя файла
            $fileName = $_FILES['photo-main']['name'];
            $filePath = __DIR__ . '/uploads/photos/';
            $fileUrl = '/uploads/photos/' . $fileName;

            move_uploaded_file($_FILES['photo-main']['tmp_name'], $filePath . $fileName);

            $data['content'] = $fileName;
        } elseif (isset($formData['photo-url']) && $postType === 'photo') {
            $data['content'] = 'privet';
            // TODO загрузить изображение по ссылке используя curl или file_get_contents
        }

        insertNewPost($db, $data);
        $postId = $db->insert_id;

        foreach ($data['tags'] as $tag) {
            $tagId = null;

            if (!selectTag($db, [$tag])) {
                insertTag($db, [$tag]);
                $tagId = $db->insert_id;
            } else {
                $tagId = selectTag($db, [$tag])['id'];
            }

            if (!selectTagToPost($db, [$tagId, $postId])){
                setTagToPost($db, [$tagId, $postId]);
            }
        }

        //  TODO 3.2 Отправить подписчикам пользователя уведомления о новом посте

        header("Location: /post.php?id={$postId}");
    }
}

$pageMainContent = includeTemplate('add.php', [
    'postTypes' => $postTypes,
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
