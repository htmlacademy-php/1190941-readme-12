<?php
/**
 * @var bool $isAuth
 * @var array $postTypes
 * @var $db
 * @var array $userData
 * @var string $scriptName
 * @var array $postsLikedByUser
 */

require 'bootstrap.php';
require 'model/posts.php';
require 'model/hashtags.php';

$queryString = $_GET ?? null;

require 'modules/filter.php';
require 'modules/like.php';

$posts = getPostsForFeed($db, $_SESSION['id'], $queryString['type']);

foreach ($posts as &$post) {
    $post['liked'] = in_array($post['id'], $postsLikedByUser);

    $post['hashtags'] = getPostTags($db, $post['id']);
}

$pageMainContent = includeTemplate('feed.php', [
    'queryString' => $queryString,
    'postTypes' => $postTypes,
    'posts' => $posts,
    'scriptName' => $scriptName,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - Моя лента',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'feed',
]);

print($pageLayout);
