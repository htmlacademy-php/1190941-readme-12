<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var array $userData
 */

require 'bootstrap.php';
require 'model/messages.php';

$queryString = $_GET ?? null;
$formData = $_POST ?? null;
$message = $_POST['message'] ?? null;

$openChats = getChats($db, $_SESSION['id'], $_SESSION['id']);

if (!isset($queryString['chat'])) {
    header('Location: /messages.php?chat=' . $openChats[0]['recipient_id'] );
}

$chatID = $queryString['chat'] ? (int) $queryString['chat'] : null;
$errors = [];

$chat = getChat($db, $chatID, $_SESSION['id']);

$isOpened = false;

if (in_array($chatID, array_column($openChats, 'recipient_id'))) {
    $isOpened = true;
}

if (!$isOpened) {
    $chatData = [];
    $usrData = selectUser($db, 'id', ['name', 'avatar_name'], [$chatID]);

    $chatData['recipient_id'] = $chatID;
    $chatData['date'] = null;
    $chatData['name'] = $usrData['name'];
    $chatData['avatar_name'] = $usrData['avatar_name'];
    $chatData['message'] = 'Сообщений нет';

    array_push($openChats, $chatData);
}

if ($formData
    && selectUser($db, 'id', ['id'], [$formData['recipientID']])
    && $formData['recipientID'] !== $_SESSION['id']
) {
    if (empty($message)) {
        $errors['title'] = 'Все упало';
        $errors['description'] = 'Это поле должно быть заполнено';
    }

    if (empty($errors)) {
        sendMessage($db, trim($message), $formData['recipientID'], $_SESSION['id']);

        header('Location: /messages.php?chat=' . $chatID);
    }
}

$pageMainContent = includeTemplate('messages.php', [
    'queryString' => $queryString,
    'openChats' => $openChats,
    'chatID' => $chatID,
    'chat' => $chat,
    'userData' => $userData,
    'errors' => $errors,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - сообщения',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'messages',
]);

print($pageLayout);
