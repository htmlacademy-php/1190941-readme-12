<?php
/**
 * @var $db
 * @var bool $isAuth
 * @var array $userData
 * @var bool $subscribed
 */

require 'bootstrap.php';

require 'model/posts.php';
require 'model/comments.php';
require 'model/hashtags.php';

$queryString = $_GET ?? null;

// TODO додумать с 0, ?id[]=343 не передавать в getPostById(), и что-то с undefined index (вспомнить где)
if (!is_string($_GET['id'])) {
    get404StatusCode();
}

$id = $_GET['id'] ?? null;

if (is_string($id)) {
    $id = intval($id);
}

$post = getPostById($db, $id);
$profileId = $post['author_id'];

require 'modules/subscriptions.php';

if (!$post) {
    get404StatusCode();
}

$comments = getPostComments($db, $id);
$hashtags = getPostTags($db, $id);

$pageMainContent = includeTemplate('post.php', [
    'post' => $post,
    'comments' => $comments,
    'hashtags' => $hashtags,
    'queryString' => $queryString,
    'subscribed' => $subscribed,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => $post['title'] . ' ▶️ Пост на Readme',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'publication',
]);

print($pageLayout);
