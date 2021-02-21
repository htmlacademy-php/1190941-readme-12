<?php
require 'functions.php';

if (!file_exists('config.php'))
{
    $msg = 'Создайте файл config.php на основе config.sample.php и внесите туда настройки сервера MySQL';
    trigger_error($msg,E_USER_ERROR);
}

$config = require 'config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli(
    $config['db']['host'],
    $config['db']['username'],
    $config['db']['password'],
    $config['db']['name'],
    $config['db']['port']
);
$db->set_charset($config['db']['charset']);

$select_post_types = "SELECT * FROM types";
$post_types = get_data($select_post_types);

if (isset($_GET['post-type'])) {
    $select_posts = "SELECT p.*,
       u.name AS author,
       u.avatar_path AS avatar,
       t.name AS type_name,
       t.class_name AS type,
       COUNT(l.post_id) AS likes_count
FROM posts p
       JOIN users u ON p.author_id = u.id
       JOIN types t ON p.type_id = t.id
       LEFT JOIN likes l ON p.id = l.post_id
WHERE t.id = ?
GROUP BY p.id
ORDER BY likes_count DESC;";
    $posts = get_prepared_data($select_posts, "i", intval($_GET['post-type']));

} else {
    $select_posts = "SELECT p.*,
       u.name AS author,
       u.avatar_path AS avatar,
       t.name AS type_name,
       t.class_name AS type,
       COUNT(l.post_id) AS likes_count
FROM posts p
       JOIN users u ON p.author_id = u.id
       JOIN types t ON p.type_id = t.id
       LEFT JOIN likes l ON p.id = l.post_id
GROUP BY p.id
ORDER BY likes_count DESC;";
    $posts = get_data($select_posts);
}

$is_auth = 1;
$user_name = 'Богдан';

$page_main_content = include_template('popular.php', [
    'posts' => $posts,
    'post_types' => $post_types
]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme - популярное',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print($page_layout);
