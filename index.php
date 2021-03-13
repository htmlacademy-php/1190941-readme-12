<?php
$query = require 'queries.php';
require 'functions.php';

$db = connect_db();

$is_auth = 1;
$user_name = 'Кто-то там';
$posts = '';

$post_types = get_data($query['post']['types']);

if ($_GET['post-type']) {
    if ($_GET['sort']) {
        $posts = get_sorted_posts();
    } else {
        $posts = get_prepared_data($query['posts']['by_types'], "i", false, intval($_GET['post-type']));
    }
} elseif ($_GET['sort']) {
    $posts = get_sorted_posts();
} else {
    $posts = get_data($query['posts']['default']);
}

$page = intval($_GET['page']);
$limit = 6;
$offset = (!$page ? 0 : $page - 1) * $limit;
$total_pages = intval(ceil(count($posts) / $limit));

$max_posts_on_page = array_slice($posts, $offset, $limit, true);

if ($page > $total_pages || $page < 0 || $_GET['page'] === '0') {
    http_response_code(404);
} elseif ($_GET['post-type'] || $_GET['post-type'] === '0') {
    $id_arr = array();

    foreach ($post_types as $post_type) {
        $id_arr[] = $post_type['id'];
    }

    if (!in_array($_GET['post-type'], $id_arr)) {
        http_response_code(404);
    }
}

$page_main_content = include_template((http_response_code()) !== 404 ? 'popular.php' : '404.php', [
    'posts_all' => $posts,
    'posts' => $max_posts_on_page,
    'post_types' => $post_types,
]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme ▶️ Популярные посты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

if ($page === 1 || $_SERVER['REQUEST_URI'] == '/index.php') {
    if ($_GET['post-type']) {
        header('Location: ' . mb_substr($_SERVER['REQUEST_URI'], 0, -mb_strlen('&page=' . $_GET['page'])));
    } else {
        header('Location: /');
    }
}

print($page_layout);
