<?php

require 'helpers.php';
require 'model/users.php';

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

if ($_SESSION) {
    $userData = selectUser($db, 'id', ['name', 'avatar_name AS avatar'], [$_SESSION['id']]);
}

$isAuth = !empty($_SESSION);

if ($isAuth && in_array($_SERVER['SCRIPT_NAME'], $availableAddresses)) {
    header('Location: /feed.php');
}
