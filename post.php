<?php
$query = require 'queries.php';
require 'functions.php';

$db = connect_db();

$is_auth = 1;
$user_name = 'Богдан';

if (isset($_GET['id'])) {
    $post = get_prepared_data($query['post']['single'], "ii", true, intval($_GET['id']), intval($_GET['id']));
    $comments = get_prepared_data($query['post']['comments'], "i", false, intval($_GET['id']));

    if (intval($_GET['id']) !== $post['id']) {
        http_response_code(404);
    }
}

$page_main_content = include_template(
    (http_response_code()) !== 404 ? 'post.php' : '404.php', [
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
