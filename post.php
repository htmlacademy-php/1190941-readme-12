<?php
/**
 * @var $db
 * @var $query
 * @var $isAuth
 * @var $userName
 */

require 'bootstrap.php';
require 'model/posts.php';
require 'model/comments.php';
require 'model/hashtags.php';

// TODO додумать с 0, ?id[]=343 не передавать в getPostById(), и что-то с undefined index (вспомнить где)
if (!is_string($_GET['id'])) {
    get404StatusCode();
}

$id = $_GET['id'] ?? null;

if (is_string($id)) {
    $id = intval($id);
}

$post = getPostById($db, $id);

if (!$post) {
    get404StatusCode();
}

$comments = getPostComments($db, $id);
$hashtags = getPostTags($db, $id);

$pageMainContent = includeTemplate('post.php', [
    'post' => $post,
    'comments' => $comments,
    'hashtags' => $hashtags,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => $post['title'] . ' ▶️ Пост на Readme',
    'isAuth' => $isAuth,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'publication',
]);

print($pageLayout);
