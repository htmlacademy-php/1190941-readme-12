<?php

function set_page_link ($is_prev, $total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri): string {
    $param = !$page ? 1 : $page;
    $page_link = $request_uri . '&page=' . (($is_prev) ? $param - 1 : $param + 1);

    if (!$has_param || $page) {
        $page_link = $script_name . '?page=' . (($is_prev) ? $param - 1 : $param + 1);

        if ($sort || $post_type) {
            $page_link = mb_substr($request_uri, 0, -mb_strlen($page)) . (($is_prev) ? $param - 1 : $param + 1);
        }
    }

    return ($param > $total_pages) ? '' : esc($page_link);
}

function pagination_button_toggler ($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri): string {
    $args = func_get_args();

    if (intval($page) === $total_pages) {
        return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
            . set_page_link(true, ...$args)
            . '">Предыдущая страница</a>';
    } elseif (!$page) {
        return '<a class="popular__page-link popular__page-link--next button button--gray" href="'
            . set_page_link(false , ...$args)
            . '">Следующая страница</a>';
    }

    return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
        . set_page_link(true, ...$args)
        . '">Предыдущая страница</a>
            <a class="popular__page-link popular__page-link--next button button--gray" href="'
        . set_page_link(false, ...$args)
        . '">Следующая страница</a>';
}

function get_sort_classes ($by, $sort, $sort_order): string {
    $class = '';

    if ($sort === $by) {
        $class = ' sorting__link--active';

        if ($sort_order === 'asc') {
            $class .= ' sorting__link--reverse';
        }
    }

    return esc($class);
}

function get_type_link ($id, $script_name): string {
    return esc($script_name . '?post-type=' . $id);
}

function get_post_link (string $id): string {
    return esc('/post.php?id=' . $id);
}

function get_sort_link ($by, $post_type, $sort, $sort_order, $script_name): string {
    $sort_link = $script_name . '?sort=' . $by . '&order=desc';

    if ($post_type) {
        $sort_link = get_type_link($post_type, $script_name) . '&sort=' . $by . '&order=desc';
    }

    if ($sort === $by) {
        if ($sort_order === 'desc') {
            $sort_link = (($post_type) ? get_type_link($post_type, $script_name) . '&'  : $script_name . '?') . 'sort=' . $by . '&order=asc';
        } else {
            $sort_link = ($post_type) ? get_type_link($post_type, $script_name) : '/';
        }
    }

    return esc($sort_link);
}

/* Queries */

function get_post_types ($db) {
    return sql_get_many($db, 'SELECT * FROM types;');
}

function get_posts ($db, $offset, $post_type = '', $sort = '', $sort_order = '', $limit = 6) {
    if ($sort) {
        $order_by = '';
        $order = 'DESC';

        if ($sort_order === 'asc') {
            $order = 'ASC';
        }

        switch ($sort) {
            case ($sort === 'popularity'):
                $order_by = "likes_count $order, comments_count $order, p.views_count $order";
                break;
            case ($sort === 'likes'):
                $order_by = "likes_count $order";
                break;
            case ($sort === 'date'):
                $order_by = "p.creation_date $order";
                break;
        }
    }

    $sql = "SELECT p.*,
             u.name AS author,
             u.avatar_name AS avatar,
             t.name AS type_name,
             t.class_name AS type,
             (SELECT COUNT(post_id)
             FROM likes l
             WHERE p.id = l.post_id) AS likes_count,
             (SELECT COUNT(post_id)
             FROM comments c
             WHERE p.id = c.post_id) AS comments_count
         FROM posts p
             JOIN users u ON p.author_id = u.id
             JOIN types t ON p.type_id = t.id
         " . (($post_type) ? 'WHERE t.id = ?' : '') . "
         ORDER BY " . (($sort) ? $order_by : 'p.views_count DESC') . "
         LIMIT ?
         OFFSET ?;";

    if ($post_type) {
        $data = sql_get_many($db, $sql, [$post_type, $limit, $offset], 'sss');
    } else {
        $data = sql_get_many($db, $sql, [$limit, $offset], 'ss');
    }

    return $data;
}

function get_pages_count ($db, $post_type = '') {

    if ($post_type) {
        return current(sql_get_single($db, '
        SELECT COUNT(*)
        FROM posts
        JOIN types t ON t.id = posts.type_id
        WHERE t.id = ?;',
        $post_type));
    }

    return current(sql_get_single($db, 'SELECT COUNT(*) FROM posts'));
}
