<?php
/**
 * @var $db
 * @var $query
 * @var $is_auth
 * @var $user_name
 */

if ($_SERVER['REQUEST_URI'] == '/index.php') {
	header('Location: /', true, 301);
}

require 'bootstrap.php';
require 'model/types.php';
require 'model/posts.php';

$query_string = $_GET ?? [];
$query_string['type'] = $query_string['type'] ?? null;
$post_types = get_post_types($db);

if ($query_string['type'] && !in_array($query_string['type'], array_column($post_types, 'id')) || $query_string['type'] === '0' || $query_string['type'] === '') {
	get_404_page($is_auth, $user_name);
}

$pages_count = get_pages_count($db, $query_string['type']);
$limit = 6;
$total_pages = intval(ceil($pages_count / $limit));
$query_string['page'] = intval($query_string['page'] ?? 1);

if ($query_string['page'] > $total_pages || $query_string['page'] <= 0) {
	get_404_page($is_auth, $user_name);
}

$offset = ($query_string['page'] - 1) * $limit;
$query_string['sort'] = $query_string['sort'] ?? null;
$query_string['direction'] = $query_string['direction'] ?? null;
$posts = get_posts($db, $offset, $query_string['type'] , $query_string['sort'], $query_string['direction']);

$pagination['prev'] = $query_string['page'] - 1;
$pagination['next'] = $query_string['page'] + 1;
$pagination['next'] = $pagination['next'] <= $total_pages ? $pagination['next'] : null;

$sort = [
    'Популярность' => 'popularity',
    'Лайки' => 'likes',
    'Дата' => 'date',
];

/* TODO сообразить как сократить код рендеринга шаблонов.
    Можно сделать функцию, которая принимает постоянные значения как простые параметры,
    а переменные - в виде массива */

$page_main_content = include_template('index.php', [
	'total_pages' => $total_pages,
	'posts' => $posts,
	'post_types' => $post_types,
	'query_string' => $query_string,
    'pagination' => $pagination,
    'sort' => $sort,
]);

$page_layout = include_template('layout.php', [
	'page_title' => 'Readme ▶️ Популярные посты',
	'is_auth' => $is_auth,
	'user_name' => $user_name,
	'page_main_content' => $page_main_content,
]);

print $page_layout;
