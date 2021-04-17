<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

// TODO еще раз пройтись по индексу от начала до конца
!($_SERVER['REQUEST_URI'] == '/posts.php') ?: header('Location: /');

require 'bootstrap.php';
require 'helpers/index.php';
require 'model/posts.php';

$params = $_GET ?? '';
$post_type = $params['post-type'] ?? '';
$post_types = get_post_types($db);

// Проверяю на пустоту что-бы отдать 404 когда ?post-type=0
if ($post_type !== '') {
    (in_array($post_type, array_column($post_types, 'id'))) ?: get_404_page($is_auth, $user_name);
}

$pages_count = get_pages_count($db, $post_type);
$limit = 6;
$total_pages = intval(ceil($pages_count / $limit));
$page = $params['page'] ?? '';

!($page > $total_pages || $page < 0 || $page === '0') ?: get_404_page($is_auth, $user_name);

$offset = (!$page ? 0 : $page - 1) * $limit;
$sort = $params['sort'] ?? '';
$order = $params['order'] ?? '';
$posts = get_posts($db, $offset, $post_type, $sort, $order);

$page_main_content = include_template('index.php', [
    'total_pages' => $total_pages,
    'posts' => $posts,
    'post_types' => $post_types,
    'post_type' => $post_type,
    'params' => $params,
    'sort' => $sort,
    'order' => $order
]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme ▶️ Популярные посты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print $page_layout;
