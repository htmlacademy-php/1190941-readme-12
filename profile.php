<?php
/**
 * @var $db
 * @var bool $isAuth
 * @var array $userData
 * @var bool $subscribed
 * @var string $scriptName
 * @var array $postsLikedByUser
 * @var array $subscriptions
 */

require 'bootstrap.php';
require 'model/posts.php';

require 'modules/like.php';

$queryString = $_GET ?? null;
$activeTab = $queryString['show'] ?? null;

$profileTabs = [
    'posts' => [
        'title' => 'Посты',
        'href' => [
            'show' => 'posts'
        ]
    ],
    'likes' => [
        'title' => 'Лайки',
        'href' => [
            'show' => 'likes'
        ]
    ],
    'subscriptions' => [
        'title' => 'Подписки',
        'href' => [
            'show' => 'subscriptions'
        ]
    ]
];

if (!$activeTab) {
    header('Location: ' . getQueryString($queryString, $profileTabs['posts']['href']));
} elseif (!array_key_exists($activeTab, $profileTabs)) {
    get404StatusCode();
}

$action = $queryString['action'] ?? null;
$profileId = $queryString['id'] ? (int)$queryString['id'] : null;
$existingProfile = selectUser($db, 'id', ['*'], [$profileId]);

if (!$existingProfile) {
    get404StatusCode();
}

require 'modules/subscriptions.php';

if ($action === 'subscribe' && $profileId !== $_SESSION['id']) {
    // TODO проверить существует ли пользователь на которого подписываюсь

    if (!$subscribed) {
        subscribe($db, $_SESSION['id'], $profileId);
    }

    header("Location: /profile.php?id={$profileId}");
    // TODO отправить пользователю уведомление о подписке

} elseif ($action === 'unsubscribe') {
    unsubscribe($db, $_SESSION['id'], $profileId);

    header("Location: /profile.php?id={$profileId}");
}

$profileData = getProfileData($db, $profileId);
$userPosts = getUserPosts($db, $profileId);

foreach ($userPosts as &$post) {
    $post['liked'] = false;

    if (in_array($post['id'], $postsLikedByUser)) {
        $post['liked'] = true;
    }
}

$userPostsLikedByUsers = getLikedPostsByAuthor($db, $profileId);
$subscribedUsers = getSubscribedUsers($db, $profileId);

foreach ($subscribedUsers as &$user) {
    $user['curr_subscribed'] = in_array($user['id'], array_column($subscriptions, 'user_id'));
}

$pageMainContent = includeTemplate('profile.php', [
    'profileTabs' => $profileTabs,
    'profileData' => $profileData,
    'activeTab' => $activeTab,
    'queryString' => $queryString,
    'profileId' => $profileId,
    'subscribed' => $subscribed,
    'scriptName' => $scriptName,
    'userPosts' => $userPosts,
    'userPostsLikedByUsers' => $userPostsLikedByUsers,
    'subscribedUsers' => $subscribedUsers,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - Моя лента',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'profile',
]);

print($pageLayout);
