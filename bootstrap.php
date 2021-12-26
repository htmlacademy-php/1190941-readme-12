<?php

use Symfony\Component\Mailer\Transport;

require 'vendor/autoload.php';

$dsn = 'smtp://2019d4305b6549:730b0e496bf4d8@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);

const TPL_DIR = 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
const PARTS_DIR = TPL_DIR . 'parts' . DIRECTORY_SEPARATOR;
const POST_PARTS_DIR = PARTS_DIR . 'post' . DIRECTORY_SEPARATOR;
const POST_PREVIEW_DIR = POST_PARTS_DIR . 'preview' . DIRECTORY_SEPARATOR;
const POST_ADD_DIR = POST_PARTS_DIR . 'add' . DIRECTORY_SEPARATOR;
const POST_ADD_FIELDSETS_DIR = POST_ADD_DIR . 'fieldsets' . DIRECTORY_SEPARATOR;

require 'helpers.php';
require 'model/users.php';

if (!file_exists('config.php')) {
    $msg = 'Создайте файл config.php на основе config.sample.php и внесите туда настройки сервера MySQL';
    trigger_error($msg, E_USER_ERROR);
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

$scriptName = preg_replace('/(\/)(\w.*)(\.php)/', '$2', $_SERVER['SCRIPT_NAME']);
