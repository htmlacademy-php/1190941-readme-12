<?php

const TPL_DIR = 'view/templates/';
const PARTS_DIR = TPL_DIR . 'parts/';
const POST_PARTS_DIR = PARTS_DIR . 'post/';
const POST_PREVIEW_DIR = POST_PARTS_DIR . 'preview/';
const POST_ADD_DIR = POST_PARTS_DIR . 'add/';
const POST_ADD_FIELDSETS_DIR = POST_ADD_DIR . 'fieldsets/';

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
