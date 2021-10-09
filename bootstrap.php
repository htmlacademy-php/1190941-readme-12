<?php

require 'helpers.php';

if (!file_exists('config.php')) {
    $msg = 'Создайте файл config.php на основе config.sample.php и внесите туда настройки сервера MySQL';
    trigger_error($msg,E_USER_ERROR);
}

$config = require 'config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['name'],
    $config['db']['port']
);

$db->set_charset($config['db']['charset']);

session_start();

$availableAddresses = ['/index.php', '/registration.php'];

if (empty($_SESSION) && !in_array($_SERVER['SCRIPT_NAME'], $availableAddresses)) {
    header('Location: /');
}

$isAuth = !empty($_SESSION);

if ($isAuth && in_array($_SERVER['SCRIPT_NAME'], $availableAddresses)) {
    header('Location: /feed.php');
}

// TODO отдельный сценарий для логаута наверное слишком много чести, уточнить это
$isLogout = $_GET['logout'] ?? '';

if ($isLogout) {
    $_SESSION = [];
    header('Location: /');
}
