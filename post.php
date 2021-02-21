<?php
require 'functions.php';

if (!file_exists('config.php'))
{
    $msg = 'Создайте файл config.php на основе config.sample.php и внесите туда настройки сервера MySQL';
    trigger_error($msg,E_USER_ERROR);
}

require 'config.php';

$db = new mysqli($db_host, $db_username, $db_password, $db_database, $db_port);
$db->set_charset($db_charset);

$is_auth = 1;
$user_name = 'Богдан';

$select_post = "SELECT p.*,
       u.name AS author,
       u.avatar_path AS avatar,
       t.name AS type_name,
       t.class_name AS type,
       COUNT(l.post_id) AS likes_count
FROM posts p
       JOIN users u ON p.author_id = u.id
       JOIN types t ON p.type_id = t.id
       LEFT JOIN likes l ON p.id = l.post_id
WHERE p.id = ?
GROUP BY p.id
ORDER BY likes_count DESC;";
$post = get_prepared_data($select_post, "i", intval($_GET['id']));

$page_main_content = include_template('post.php', ['post' => $post]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme - популярное',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print($page_layout);
