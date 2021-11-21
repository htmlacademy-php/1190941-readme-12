<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var array $userData
 * @var string $scriptName
 */

require 'bootstrap.php';
require 'model/messages.php';

$queryString = $_GET ?? null;
$formData = $_POST ?? null;
$message = $_POST['message'] ?? null;
$chatID = $queryString['chat'] ?? null;



$errors = [];

$chat = getChat($db, [$chatID, $_SESSION['id'], $_SESSION['id'], $chatID]);

// qstn нужна помощь, не понимаю как составить запрос на получение чатов
$openChats = [
    [
        'id' => '3',
        'date' => '2021-11-20 16:03:36',
        'name' => 'Марк Смолов',
        'avatar' => 'userpic-mark.jpg',
        'message' => 'Алло'
    ], [
        'id' => '4',
        'date' => '2021-11-20 16:13:36',
        'name' => 'Эльвира Хайпулинова',
        'avatar' => 'userpic-elvira.jpg',
        'message' => 'Алло2'
    ], [
        'id' => '5',
        'date' => '2021-11-20 16:23:36',
        'name' => 'Петр Демин',
        'avatar' => 'userpic-petro.jpg',
        'message' => 'Алло3'
    ],
];

if (!$chatID) {
    header('Location: /messages.php?chat=' . $openChats[0]['id'] );
}

$isOpened = null;

foreach ($openChats as $openChat) {
    if (in_array($chatID, $openChat)) {
        $isOpened = true;
    }
}

if (!$isOpened) {
    $chatData = [];
    $usrData = selectUser($db, 'id', ['name', 'avatar_name'], [$chatID]);

    $chatData['id'] = $chatID;
    $chatData['date'] = null;
    $chatData['name'] = $usrData['name'];
    $chatData['avatar'] = $usrData['avatar_name'];
    $chatData['message'] = null;

    array_push($openChats, $chatData);
}

// todo валидацию формы можно подать отдельным сценарием
if ($formData
    && selectUser($db, 'id', ['id'], [$formData['recipientID']])
    && $formData['recipientID'] !== $_SESSION['id']
) {
    if (empty($message)) {
        $errors['title'] = 'Все упало';
        $errors['description'] = 'Это поле должно быть заполнено';
    }

    if (empty($errors)) {
        $data['message'] = trim($message);
        $data['recipient_id'] = $formData['recipientID'];
        $data['sender_id'] = $_SESSION['id'];

        sendMessage($db, array_values($data));

        header('Location: /messages.php?chat=' . $chatID);
    }
}

// todo заходить в интерфейс переписки могут только подписанные пользователи, остальным denied

$pageMainContent = includeTemplate('messages.php', [
    'queryString' => $queryString,
    'scriptName' => $scriptName,
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
