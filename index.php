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

$params = $_GET ?? [];
$post_type = $params['post-type'] ?? null;
$post_types = get_post_types($db);

if ($post_type && !in_array($post_type, array_column($post_types, 'id')) || $post_type === '0' || $post_type === '') {
    get_404_page($is_auth, $user_name);
}

$pages_count = get_pages_count($db, $post_type);
$limit = 6;
$total_pages = intval(ceil($pages_count / $limit));
$current_page = intval($params['page'] ?? 1);

if ($current_page > $total_pages || $current_page <= 0) {
    get_404_page($is_auth, $user_name);
}

$offset = ($current_page - 1) * $limit;
$sort = $params['sort'] ?? '';
$order = $params['order'] ?? '';
$posts = get_posts($db, $offset, $post_type, $sort, $order);

function set_page_link (array $params, bool $is_prev = false): string {
    $post_type = $params['post-type'] ?? '';
    $page = intval($params['page'] ?? 1);

    if ($is_prev) {
        if ($page === 2) {
            $page_link = ($post_type) ? '?' . http_build_query(array_filter($params, function ($param) {
                    return $param !== 'page';
                }, ARRAY_FILTER_USE_KEY))
                : '/';
        } else {
            $params['page'] = $page - 1;
            $page_link = '?' . http_build_query($params);
        }
    } else {
        $params['page'] = ($page === 1) ? 2 : $page + 1;
        $page_link = '?' . http_build_query($params);
    }

    return esc($page_link);
}

function pagination_button_toggle (int $total_pages, array $params): string {
    $page = intval($params['page'] ?? 1);

    if ($page === $total_pages) {
        return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
            . set_page_link($params, true)
            . '">Предыдущая страница</a>';
    } elseif ($page === 1) {
        return '<a class="popular__page-link popular__page-link--next button button--gray" href="'
            . set_page_link($params)
            . '">Следующая страница</a>';
    }

    return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
        . set_page_link($params, true)
        . '">Предыдущая страница</a>
            <a class="popular__page-link popular__page-link--next button button--gray" href="'
        . set_page_link($params)
        . '">Следующая страница</a>';
}

function get_sort_classes (string $by, string $sort, string $sort_order): string {
    $class = '';

    if ($sort === $by) {
        $class = ' sorting__link--active';
        if ($sort_order === 'asc') {
            $class .= ' sorting__link--reverse';
        }
    }

    return esc($class);
}

function set_type_link (string $id): string {
    return esc('?post-type=' . $id);
}

function set_post_link (string $id): string {
    return esc('/post.php?id=' . $id);
}

function set_sort_link (string $by, array $params): string {
    $params = array_filter($params, function ($param) {
        return $param !== 'page';
    }, ARRAY_FILTER_USE_KEY);
    $sort = $params['sort'] ?? '';
    $order = $params['order'] ?? '';
    $post_type = $params['post-type'] ?? '';

    if ($sort === $by) {
        if ($order === 'asc') {
            $params = array_filter($params, function ($param) {
                return ($param === 'sort' || $param ===  'order') ? '' : $param;
            }, ARRAY_FILTER_USE_KEY);

            $sort_link = (!$post_type) ? '/' : '?' . http_build_query($params);
        } else {
            $params['order'] = 'asc';
            $sort_link = '?' . http_build_query($params);
        }
    } else {
        if ($post_type) {
            if ($sort) {
                $params['sort'] = $by;
                $params['order'] = 'desc';
                $sort_link = '?' . http_build_query($params);
            } else {
                $sort_link = '?' . http_build_query($params) . '&sort=' . $by . '&order=desc';
            }
        } else {
            $sort_link = '?sort=' . $by . '&order=desc';
        }
    }

    return esc($sort_link);
}

$page_main_content = include_template('index.php', [
    'total_pages' => $total_pages,
    'current_page' => $current_page,
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
