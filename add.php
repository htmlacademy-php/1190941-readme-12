<?php

/**
 * @var $db
 * @var $is_auth
 * @var $user_name
 */

require 'bootstrap.php';
require 'model/types.php';

var_dump($_POST);
$post_types = get_post_types($db);

$page_main_content = include_template('add.php', [
    'post_types' => $post_types,
]);

$page_layout = include_template('layout.php', [
    'page_title' => 'Добавить новый пост ▶️ Пост на Readme',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print($page_layout);
