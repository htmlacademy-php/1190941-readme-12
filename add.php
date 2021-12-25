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

$queryString = $_GET ?? null;
$postType = $queryString['type'] ?? null;

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
        if (!in_array($name, $noValidateFields) && empty($value)) {
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
            && !filter_var($formData["{$formDataPostType}-main"], FILTER_VALIDATE_URL))) {

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

        $tags = explode(' ', $_POST["{$formDataPostType}-tags"]) ?? null;
        $typeID = current(array_filter($postTypes, function ($type) use ($formDataPostType)
        {
            return $type['class_name'] === $formDataPostType;
        }))['id'];

        $content = $formData["{$formDataPostType}-main"] ?? null;

        if (isset($isFile) && $isFile['error'] === 0) {
            // FIXME сгенерировать имя файла
            $fileName = $_FILES['photo-main']['name'];
            $filePath = __DIR__ . '/uploads/photos/';
            $fileUrl = '/uploads/photos/' . $fileName;

            move_uploaded_file($_FILES['photo-main']['tmp_name'], $filePath . $fileName);

            $content = $fileName;
        } elseif (isset($formData['photo-url']) && $formDataPostType === 'photo') {
            $content = 'privet';
            // TODO загрузить изображение по ссылке используя curl или file_get_contents
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

        foreach ($tags as $tag) {
            $tagId = null;

            if (!selectTag($db, $tag)) {
                insertTag($db, $tag);
                $tagId = $db->insert_id;
            } else {
                $tagId = selectTag($db, $tag)['id'];
            }

            if (!selectTagToPost($db, $tagId, $postId)){
                setTagToPost($db, $tagId, $postId);
            }
        }

        //  TODO 3.2 Отправить подписчикам пользователя уведомления о новом посте

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
