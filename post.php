<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

require 'bootstrap.php';
require 'model/posts.php';
require 'model/comments.php';

// TODO додумать с 0, не передавать в get_post_by_id()
$id = intval($_GET['id'] ?? 0);
$post = get_post_by_id($db, $id);

// TODO додумать с ?id[]=343 и undefined index
if (!$post) {
    get_404_page($is_auth, $user_name);
}

$comments = get_post_comments($db, $id);

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
