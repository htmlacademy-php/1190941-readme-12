<?php
/**
 * @var $db
 */

require 'bootstrap.php';
require 'model/users.php';

if ($_SERVER['REQUEST_URI'] == '/index.php') {
    header('Location: /', true, 301);
}

$formData = $_POST ?? null;
$userData = getUser($db, [$formData['email'] ?? null]);
$isError = null;

if ($formData) {
    if ($userData && password_verify($formData['password'], $userData['password'])) {
        $isError = false;

        $_SESSION = array_filter($userData, function ($key) {
            return $key !== 'password' ? $key : null;
        }, ARRAY_FILTER_USE_KEY);
        header('Location: /feed.php');
    }

    $isError = true;
}

$pageLayout = includeTemplate('main.php', [
    'isError' => $isError,
]);

print($pageLayout);
