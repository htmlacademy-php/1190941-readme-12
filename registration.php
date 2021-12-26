<?php
/**
 * @var $db
 * @var int $isAuth
 * @var string $userName
 */

require 'bootstrap.php';

$formData = $_POST ?? null;

$fieldsMap = [
    'email' => 'Электронная почта',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Повтор пароля',
    'avatar-file' => 'Аватар пользователя',
];
$errors = [];

if (!empty($formData)) {
    foreach ($formData as $name => $value) {
        if (empty($value)) {
            $errors[$name]['name'] = $fieldsMap[$name] ?? null;
            $errors[$name]['title'] = 'Поле не заполнено';
            $errors[$name]['description'] = 'Это поле должно быть заполнено';
        }
    }

    if (!empty($formData['email'])) {
        if (selectUserByEmail($db, $formData['email'])) {
            $errors['email']['name'] = $fieldsMap['email'];
            $errors['email']['title'] = 'Адрес электронной почты уже используется';
            $errors['email']['description'] = 'Если вы являетесь владельцем данной электронной почты пожалуйста воспользуйтесь страницей входа в аккаунт';

        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email']['name'] = $fieldsMap['email'];
            $errors['email']['title'] = 'Адрес электронной почты введен не корректно';
            $errors['email']['description'] = 'Введите корректный адрес электронной почты';
        }
    }

    if (empty($formData['password']) || empty($formData['password-repeat'])) {
        $errors['password']['name'] = $fieldsMap['password'];
        $errors['password']['title'] = 'Поле не заполнено';
        $errors['password']['description'] = 'Это поле должно быть заполнено';
        $errors['password-repeat']['name'] = $fieldsMap['password-repeat'];
        $errors['password-repeat']['title'] = 'Поле не заполнено';
        $errors['password-repeat']['description'] = 'Это поле должно быть заполнено';
    }

    if (!empty($formData['password'])
        && !empty($formData['password-repeat'])
        && $formData['password'] !== $formData['password-repeat']
    ) {
        $errors['password']['name'] = $fieldsMap['password'];
        $errors['password']['title'] = 'Введенные пароли не совпадают';
        $errors['password']['description'] = 'Введите одинаковый пароль в оба поля';
    }

    $avatar = $_FILES['avatar-file'] ?? null;

    if ($avatar['error'] === 0) {
        $fileTempName = $_FILES['avatar-file']['tmp_name'];
        $mimeType = mime_content_type($fileTempName);
        $acceptedMimeTypes = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];

        if (!in_array($mimeType, $acceptedMimeTypes)) {
            $errors['avatar-file']['name'] = $fieldsMap['avatar-file'];
            $errors['avatar-file']['title'] = 'Не верный формат изображения';
            $errors['avatar-file']['description'] = 'Пожалуйста загрузите аватар в одном из форматов - png, jpeg, gif';
        }
    }

    if (empty($errors)) {
        $avatarName = null;

        if ($avatar['error'] === 0) {
            $fileName = $_FILES['avatar-file']['name'];
            $filePath = __DIR__ . '/uploads/avatars/';
            $fileUrl = '/uploads/avatars/' . $fileName;

            move_uploaded_file($_FILES['avatar-file']['tmp_name'], $filePath . $fileName);

            $avatarName = $fileName;
        }

        createUser(
            $db,
            $formData['login'],
            $formData['email'],
            password_hash($formData['password'], PASSWORD_DEFAULT),
            $avatarName
        );
        $userId = $db->insert_id;

        header("Location: /");
    }
}

$pageMainContent = includeTemplate('registration.php', [
    'errors' => $errors,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - популярное',
    'isAuth' => $isAuth,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'registration',
]);

print($pageLayout);
