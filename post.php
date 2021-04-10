<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

require 'bootstrap.php';
require 'functions/post.php';

$post = [];
$comments = '';
$id = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if (isset($_GET['id'])) {
    if ($_GET['id'] === '0') {
        get_404_page($is_auth, $user_name);
        exit();
    } else {
        $post = get_post_by_id($db, $id);
        $comments = get_post_comments($db, $id);

        if (intval($_GET['id']) !== $post['id']) {
            get_404_page($is_auth, $user_name);
            exit();
        }
    }
}

$page_main_content = include_template('post.php', [
    'post' => $post,
    'comments' => $comments,
]);

$page_layout = include_template('layout.php', [
    'page_title' => $post['title'] . ' ▶️ Пост на Readme',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print($page_layout);
