<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

require 'bootstrap.php';
require 'functions/index.php';

define('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
define('REQUEST_URI', $_SERVER['REQUEST_URI']);

$posts = '';
$page = '';
$sort = '';
$sort_order = '';
$post_type = '';
$has_param = false;

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}

if (isset($_GET['sort']) && isset($_GET['order'])) {
    $sort = $_GET['sort'];
    $sort_order = $_GET['order'];
}

if (isset($_GET['post-type'])) {
    $post_type = $_GET['post-type'];
}

if (!empty($_GET)) {
    $has_param = true;
}

$limit = 6;
$offset = (!$page ? 0 : $page - 1) * $limit;

$pages_count = get_pages_count($db);
$post_types = get_post_types($db);

if ($post_type !== '0') {
    $pages_count = get_pages_count($db, $post_type);

    if ($sort) {
        $posts = get_posts($db, $offset, $post_type, $sort, $sort_order);
    } else {
        $posts = get_posts($db, $offset, $post_type);
    }
} elseif ($sort) {
    $posts = get_posts($db, $offset, '', $sort, $sort_order);
} else {
    $posts = get_posts($db, $offset);
}

$total_pages = intval(ceil($pages_count / $limit));

if ($page > $total_pages || $page < 0 || $page === '0') {
    get_404_page($is_auth, $user_name);
    exit();
} elseif ($post_type) {
    $id_arr = array();

    foreach ($post_types as $type) {
        $id_arr[] = $type['id'];
    }

    if (!in_array($post_type, $id_arr) || $post_type === '0') {
        get_404_page($is_auth, $user_name);
        exit();
    }
}

$page_main_content = include_template('index.php', [
    'total_pages' => $total_pages,
    'posts' => $posts,
    'post_types' => $post_types,
    'post_type' => $post_type,
    'has_param' => $has_param,
    'page' => $page,
    'sort' => $sort,
    'sort_order' => $sort_order,
]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme ▶️ Популярные посты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

if ($page === 1 || REQUEST_URI == '/index.php') {
    if ($post_type) {
        header('Location: ' . mb_substr(REQUEST_URI, 0, -mb_strlen('&page=' . $page)));
    } else {
        header('Location: /');
    }
}

print $page_layout;
