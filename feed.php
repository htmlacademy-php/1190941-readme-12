<?php
/**
 * @var bool $isAuth
 * @var array $postTypes
 * @var $db
 * @var array $userData
 */

require 'bootstrap.php';
require 'model/posts.php';

$queryString = $_GET ?? null;

require 'modules/filter.php';
require 'modules/like.php';

$posts = getPostsForFeed($db, $_SESSION['id'], $queryString['type']);

$pageMainContent = includeTemplate('feed.php', [
    'queryString' => $queryString,
    'postTypes' => $postTypes,
    'posts' => $posts,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - Моя лента',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'feed',
]);

print($pageLayout);
