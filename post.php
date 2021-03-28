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

if (isset($_GET['id'])) {
    $post = get_post_by_id($db);
    $comments = get_post_comments($db);

    if (intval($_GET['id']) !== $post['id']) {
        http_response_code(404);
    }
}

if (http_response_code() === 404) {
    require '404.php';
} else {
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
}
