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
$query_string['type'] = $_GET['type'] ?? '';
$post_types = get_post_types($db);

// TODO проверить по итогу всех правок
//if ($query_string['type']  && !in_array($query_string['type'], array_column($post_types, 'id')) || $query_string['type'] === '0' || $query_string['type'] === '') {
//	get_404_page($is_auth, $user_name);
//}

$pages_count = get_pages_count($db, $query_string['type']);
$limit = 6;
$total_pages = intval(ceil($pages_count / $limit));
$query_string['page'] = intval($query_string['page'] ?? 1);

if ($query_string['page'] > $total_pages || $query_string['page'] <= 0) {
	get_404_page($is_auth, $user_name);
}

$offset = ($query_string['page'] - 1) * $limit;
$query_string['sort'] = $query_string['sort'] ?? '';
$query_string['order'] = $query_string['order'] ?? '';
$posts = get_posts($db, $offset, $query_string['type'] , $query_string['sort'], $query_string['order']);

$pagination['prev'] = $query_string['page'] - 1;
$pagination['next'] = $query_string['page'] + 1;

// TODO по итогу доработок удалить комментарий
//function set_link ($pagination, $query_string, $set_sort = []): string {
//    $query_params = [];
//    !$post_type ?: $query_params['post-type'] = $post_type;
//
//    if ($sort) {
//        $query_params['sort'] = $sort;
//        $query_params['order'] = $order;
//    }
//
//    if ($set_sort) {
//        if ($set_sort === $sort) {
//            if ($order === 'asc') {
//                unset($query_params['sort'], $query_params['order']);
//            } else {
//                $query_params['order'] = 'asc';
//            }
//        } else {
//            $query_params['sort'] = $set_sort;
//            $query_params['order'] = 'desc';
//        }
//        unset($query_params['page']);
//    }
//
//    return $query_params ? '?' . http_build_query($query_params) : '/';
//}

$sort = [
    'Популярность' => 'popularity',
    'Лайки' => 'likes',
    'Дата' => 'date',
];

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
