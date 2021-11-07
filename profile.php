<?php
/**
 * @var $db
 * @var bool $isAuth
 * @var array $userData
 * @var bool $subscribed
 */

require 'bootstrap.php';

$queryString = $_GET ?? null;
$action = $queryString['action'] ?? null;
$profileId = $queryString['id'] ? +$queryString['id'] : null;
$existingProfile = selectUser($db, 'id', ['*'], [$profileId]);

if (!$existingProfile) {
    get404StatusCode();
}

require 'modules/subscriptions.php';

if ($action === 'subscribe' && $profileId !== $_SESSION['id']) {
    if (!$subscribed) {
        subscribe($db, [$_SESSION['id'], $profileId]);
    }

    header("Location: /profile.php?id={$profileId}");
    // TODO отправить пользователю уведомление о подписке
} elseif ($action === 'unsubscribe' && $subscribed) {
    unsubscribe($db, [$_SESSION['id'], $profileId]);

    header("Location: /profile.php?id={$profileId}");
}

$profileData = getProfileData($db, $profileId);

$pageMainContent = includeTemplate('profile.php', [
    'profileData' => $profileData,
    'profileId' => $profileId,
    'subscribed' => $subscribed,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - Моя лента',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'profile',
]);

print($pageLayout);
