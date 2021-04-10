<?php

function set_page_link ($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri,  $is_prev = false): string {
    $param = !$page ? 1 : $page;

    if (!$has_param || $page && !$sort && !$post_type) {
        $page_link = $script_name . '?page=' . (($is_prev) ? $param - 1 : $param + 1);
    } elseif ($page && $sort || $page && $post_type) {
        $page_link = mb_substr($request_uri, 0, -mb_strlen($page)) . (($is_prev) ? $param - 1 : $param + 1);
    } else {
        $page_link = $request_uri . '&page=' . (($is_prev) ? $param - 1 : $param + 1);
    }

    if ($is_prev) {
        return $param !== 1 ? esc($page_link) : '';
    }

    return ($param >= $total_pages) ? '' : esc($page_link);
}

function pagination_button_toggler ($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri): string {
    if (intval($page) === $total_pages) {
        return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
            . set_page_link($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri, true)
            . '">Предыдущая страница</a>';
    } elseif (!$page) {
        return '<a class="popular__page-link popular__page-link--next button button--gray" href="'
            . set_page_link($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri)
            . '">Следующая страница</a>';
    }

    return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
        . set_page_link($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri, true)
        . '">Предыдущая страница</a>
            <a class="popular__page-link popular__page-link--next button button--gray" href="'
        . set_page_link($total_pages, $page, $sort, $post_type, $has_param, $script_name, $request_uri)
        . '">Следующая страница</a>';
}

function get_sort_classes ($for, $sort, $sort_order): string {
    $correct_class = '';

    if ($sort === $for) {
        $correct_class = ' sorting__link--active';

        if ($sort_order === 'asc') {
            $correct_class .= ' sorting__link--reverse';
        }
    }

    return esc($correct_class);
}

function get_type_link ($id, $script_name): string {
    return esc($script_name . '?post-type=' . $id);
}

function get_post_link (string $id): string {
    return esc('/post.php?id=' . $id);
}

function get_sort_link ($for, $page, $post_type, $sort, $sort_order, $script_name, $request_uri, $has_param): string {
    $sort_link = $script_name . '?sort=' . $for . '&order=desc';

    if ($has_param) {
        if ($sort === $for) {
            if ($post_type) {
                $sort_link = $script_name . '?post-type=' . $post_type . '&sort=' . $for . '&order=desc';

                if ($sort_order === 'desc') {
                    $sort_link = mb_substr($request_uri, 0, -mb_strlen($sort_order)) . 'asc';

                    if ($page) {
                        $sort_link = mb_substr($request_uri, 0, -mb_strlen($sort_order . '&page=' . $page)) . 'asc';
                    }
                } elseif ($sort_order === 'asc') {
                    $sort_link = $script_name . '?post-type=' . $post_type;
                }
            } else {
                if ($sort_order === 'desc') {
                    $sort_link = mb_substr($script_name . '?sort=' . $for . '&order=' . $sort_order, 0, -mb_strlen($sort_order)) . 'asc';
                } elseif ($sort_order === 'asc') {
                    $sort_link = '/';
                }
            }
        } elseif ($post_type) {
            $sort_link = $script_name . '?post-type=' . $post_type . '&sort=' . $for . '&order=desc';
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
             COUNT(l.post_id) AS likes_count,
             COUNT(DISTINCT c.post_id) AS comments_count
         FROM posts p
             JOIN users u ON p.author_id = u.id
             JOIN types t ON p.type_id = t.id
             LEFT JOIN likes l ON p.id = l.post_id
             LEFT JOIN comments c ON p.id = c.post_id
         " . (($post_type) ? 'WHERE t.id = ?' : '') . "
         GROUP BY " . (($sort) ? 'p.id' : 'p.id, p.views_count') . "
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

function get_pages_count ($db, $post_type, $is_typed = false) {

    if ($is_typed) {
        return current(sql_get_single($db, '
        SELECT COUNT(*)
        FROM posts
        JOIN types t ON t.id = posts.type_id
        WHERE t.id = ?;',
        $post_type));
    }

    return current(sql_get_single($db, 'SELECT COUNT(*) FROM posts'));
}
