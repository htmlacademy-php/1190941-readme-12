<?php
require 'helpers.php';

$is_auth = rand(0, 1);

$user_name = 'Богдан';

$posts = [
    [
        'header' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user-name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'header' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'user-name' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
    [
        'header' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'user-name' => 'Виктор',
        'avatar' => 'userpic-mark.jpg',
    ],
    [
        'header' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'user-name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'header' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user-name' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
    [
        'header' => '<script>alert("XSS");</script>',
        'type' => 'post-text',
        'content' => '<script>alert("XSS");</script>',
        'user-name' => '<script>alert("XSS");</script>',
        'avatar' => 'userpic.jpg',
    ],
    [
        'header' => '<script>alert("XSS");</script>',
        'type' => 'post-link',
        'content' => '"/><script>alert("XSS");</script>',
        'user-name' => 'Бандит02',
        'avatar' => 'userpic.jpg',
    ],
];

$current_date = new DateTime('now', new DateTimeZone('Europe/Kiev'));
$current_date = $current_date->format('Y-m-d h:i:s');

function get_date_time_diff (string $current_date, string $post_date) {
    $cur_date = new DateTime($current_date);
    $p_date = new DateTime($post_date);

    $date_diff = $cur_date->diff($p_date);
    $date_diff->format('h часов');

    return $date_diff;
}

foreach ($posts as $post_key => &$post_value) {
    $post_date = generate_random_date($post_key);
    $to_date_time = new DateTime($post_date);
    $to_date_time->format('Y-m-d h:i:s');
    $time_ago = get_date_time_diff($current_date, $to_date_time);
    $post_value['date'] = $time_ago;
}

function crop_text (string $text, int $max_chars = 300) {
    if (mb_strlen($text) < $max_chars) {
        return $text;
    }

    $text_parts = explode(' ', $text);
    $total_chars = 0;
    $space_value = 1;
    $verified_text = array();

    foreach ($text_parts as $text_part) {
        $total_chars += mb_strlen($text_part) + $space_value;

        if (($total_chars - $space_value) >= $max_chars) {
            break;
        }

        $verified_text[] = $text_part;
    }

    $text = implode(' ', $verified_text);

    return $text . '...';
}

$page_main_content = include_template('main.php', ['posts' => $posts]);
$page_layout = include_template('layout.php', [
    'page_title' => 'Readme - популярное',
    'current_date' => $current_date,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,

]);

print($page_layout);

