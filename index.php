<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

require 'bootstrap.php';
require 'functions/index.php';

$posts = '';
$pages_count = '';

$page = intval($_GET['page']);
$limit = 6;
$offset = (!$page ? 0 : $page - 1) * $limit;

$post_types = get_post_types($db);

if ($_GET['post-type']) {
    if ($_GET['sort']) {
        $pages_count = get_pages_count($db, true);
        $posts = get_sorted_posts($db, $offset);
    } else {
        $pages_count = get_pages_count($db, true);
        $posts = get_posts($db, $offset, true);
    }
} elseif ($_GET['sort']) {
    $pages_count = get_pages_count($db);
    $posts = get_sorted_posts($db, $offset);
} else {
    $pages_count = get_pages_count($db);
    $posts = get_posts($db, $offset);
}

$total_pages = intval(ceil($pages_count / $limit));

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

if (http_response_code() === 404) {
    require '404.php';
} else {
    $page_main_content = include_template('index.php', [
        'total_pages' => $total_pages,
        'posts' => $posts,
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
}


