<?php
/**
 * @var $db
 */

require 'bootstrap.php';
require 'model/users.php';

if ($_SERVER['REQUEST_URI'] == '/index.php') {
    header('Location: /', true, 301);
}

$action = $_GET['action'] ?? null;
$isLogout = $action === 'logout';

if ($isLogout) {
    $_SESSION = [];
}

$formData = $_POST ?? null;
$userData = selectUser($db, 'email', ['id', 'password'], [$formData['email'] ?? null]);
$isError = null;

if ($formData) {
    if ($userData && password_verify($formData['password'], $userData['password'])) {
        $isError = false;

        $_SESSION['id'] = $userData['id'];
        header('Location: /feed.php');
    }

    $isError = true;
}

$pageLayout = includeTemplate('main.php', [
    'isError' => $isError,
]);

print($pageLayout);
