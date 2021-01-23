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
];

function show_title_date_format (string $date_time): string {
    $date_time = new DateTime($date_time, new DateTimeZone('Europe/Moscow'));
    return $date_time->format('d-m-Y H:i');
}

function get_relative_date_format (string $post_date): string {
    $post_date = new DateTime($post_date, new DateTimeZone('Europe/Moscow'));
    $current_date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $date_time_diff = $post_date->diff($current_date);

    if ($date_time_diff->m !== 0) {
        $months = $date_time_diff->m;
        return "{$months} " . get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев') . " назад";
    }

    if ($date_time_diff->d >= 7) {
        $weeks = floor($date_time_diff->d / 7);
        return "{$weeks} " . get_noun_plural_form($weeks, 'неделю', 'недели', 'недели') . " назад";
    }

    if ($date_time_diff->d < 7 && $date_time_diff->d !== 0) {
        $days = $date_time_diff->d;
        return "{$days} " . get_noun_plural_form($days, 'день', 'дня', 'дней') . " назад";
    }

    if ($date_time_diff->h !== 0) {
        $hours = $date_time_diff->h;
        return "{$hours} " . get_noun_plural_form($hours, 'час', 'часа', 'часов') . " назад";
    }

    if ($date_time_diff->i !== 0) {
        $minutes = $date_time_diff->i;
        return "{$minutes} " . get_noun_plural_form($minutes, 'минуту', 'минуты', 'минут') . " назад";
    }
}

foreach ($posts as $post_key => &$post_value) {
    $post_date = generate_random_date($post_key);
    $post_value['date'] = $post_date;
}

function crop_text (string $text, int $max_chars = 300): string {
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
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_main_content' => $page_main_content,
]);

print($page_layout);
