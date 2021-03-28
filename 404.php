<?php

$is_auth = 1;
$user_name = 'Кто-то там';

$page_layout = include_template('layout.php', [
    'page_title' => 'Readme ▶️ 404',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => include_template('404.php'),
]);

print($page_layout);
