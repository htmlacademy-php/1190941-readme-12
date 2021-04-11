<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

if ($_SERVER['REQUEST_URI'] == '/index.php') {
    header('Location: /');
}

require 'bootstrap.php';
require 'model/index.php';

$post_types = get_post_types($db);
$post_type = '';

if (isset($_GET['post-type'])) {
    $post_type = $_GET['post-type'];

    if (!in_array($post_type, array_column($post_types, 'id')) || $post_type === '0') {
        get_404_page($is_auth, $user_name);
    }
}

$page = '';

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);

    if ($page === 1) {
        if ($post_type) {
            header('Location: ' . mb_substr($_SERVER['REQUEST_URI'], 0, -mb_strlen('&page=' . $page)));
        } else {
            header('Location: /');
        }
    }
}

$limit = 6;
$pages_count = get_pages_count($db, $post_type);
$total_pages = intval(ceil($pages_count / $limit));

if ($page > $total_pages || $page < 0 || $page === '0') {
    get_404_page($is_auth, $user_name);
}

$sort = '';
$sort_order = '';

if (isset($_GET['sort']) && isset($_GET['order'])) {
    $sort = $_GET['sort'];
    $sort_order = $_GET['order'];
}

$offset = (!$page ? 0 : $page - 1) * $limit;
$posts = get_posts($db, $offset, $post_type, $sort, $sort_order);

$has_param = false;

if (!empty($_GET)) {
    $has_param = true;
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


print $page_layout;
