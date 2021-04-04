<?php

function set_page_link (int $total_pages, bool $is_prev = false): string {
    $param = !isset($_GET['page']) ? 1 : $_GET['page'];

    if (empty($_GET) || isset($_GET['page']) && !isset($_GET['sort']) && !isset($_GET['post-type'])) {
        $page_link = $_SERVER['SCRIPT_NAME'] . '?page=' . (($is_prev) ? $param - 1 : $param + 1);
    } elseif (isset($_GET['page']) && isset($_GET['sort']) || isset($_GET['page']) && isset($_GET['post-type'])) {
        $page_link = mb_substr($_SERVER['REQUEST_URI'], 0, -mb_strlen($_GET['page'])) . (($is_prev) ? $param - 1 : $param + 1);
    } else {
        $page_link = $_SERVER['REQUEST_URI'] . '&page=' . (($is_prev) ? $param - 1 : $param + 1);
    }

    if ($is_prev) {
        return $param !== 1 ? esc($page_link) : '';
    }

    return ($param >= $total_pages) ? '' : esc($page_link);
}

function pagination_button_toggler ($total_pages): string {
    if (isset($_GET['page']) && intval($_GET['page']) === $total_pages) {
        return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
            . set_page_link($total_pages, true)
            . '">Предыдущая страница</a>';
    } elseif (!isset($_GET['page']) || isset($_GET['post-type']) && !isset($_GET['page'])) {
        return '<a class="popular__page-link popular__page-link--next button button--gray" href="'
            . set_page_link($total_pages)
            . '">Следующая страница</a>';
    }

    return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
        . set_page_link($total_pages, true)
        . '">Предыдущая страница</a>
            <a class="popular__page-link popular__page-link--next button button--gray" href="'
        . set_page_link($total_pages)
        . '">Следующая страница</a>';
}

function get_sort_classes (string $for): string {
    $correct_class = '';

    if (isset($_GET['sort']) && $_GET['sort'] === $for) {
        $correct_class = ' sorting__link--active';

        if (isset($_GET['order']) && $_GET['order'] === 'asc') {
            $correct_class .= ' sorting__link--reverse';
        }
    }

    return esc($correct_class);
}

function get_type_link (string $id): string {
    return esc($_SERVER['SCRIPT_NAME'] . '?post-type=' . $id);
}

function get_post_link (string $id): string {
    return esc('/post.php?id=' . $id);
}

function get_sort_link (string $for): string {
    $sort_link = $_SERVER['SCRIPT_NAME'] . '?sort=' . $for . '&order=desc';

    if (!empty($_GET)) {
        if (isset($_GET['sort']) && $_GET['sort'] === $for) {
            if (isset($_GET['post-type'])) {
                $sort_link = $_SERVER['SCRIPT_NAME'] . '?post-type=' . $_GET['post-type'] . '&sort=' . $for . '&order=desc';

                if (isset($_GET['order']) && $_GET['order'] === 'desc') {
                    $sort_link = mb_substr($_SERVER['REQUEST_URI'], 0, -mb_strlen($_GET['order'])) . 'asc';

                    if (isset($_GET['page'])) {
                        $sort_link = mb_substr($_SERVER['REQUEST_URI'], 0, -mb_strlen($_GET['order'] . '&page=' . $_GET['page'])) . 'asc';
                    }
                } elseif (isset($_GET['order']) && $_GET['order'] === 'asc') {
                    $sort_link = $_SERVER['SCRIPT_NAME'] . '?post-type=' . $_GET['post-type'];
                }
            } else {
                if (isset($_GET['order']) && $_GET['order'] === 'desc') {
                    $sort_link = mb_substr($_SERVER['SCRIPT_NAME'] . '?sort=' . $for . '&order=' . $_GET['order'], 0, -mb_strlen($_GET['order'])) . 'asc';
                } elseif (isset($_GET['order']) && $_GET['order'] === 'asc') {
                    $sort_link = '/';
                }
            }
        } elseif (isset($_GET['post-type'])) {
            $sort_link = $_SERVER['SCRIPT_NAME'] . '?post-type=' . $_GET['post-type'] . '&sort=' . $for . '&order=desc';
        }
    }

    return esc($sort_link);
}

/* Queries */

function get_post_types ($db) {
    return sql_get_many($db, 'SELECT * FROM types;');
}

function get_posts ($db, $offset, $is_typed = false, $is_sorted = false, $limit = 6) {
    if ($is_sorted) {
        $order_by = '';
        $order = 'DESC';

        if ($_GET['order'] === 'asc') {
            $order = 'ASC';
        }

        switch ($_GET['sort'] && $_GET['order']) {
            case ($_GET['sort'] === 'popularity'):
                $order_by = "likes_count {$order}, comments_count {$order}, p.views_count {$order}";
                break;
            case ($_GET['sort'] === 'likes'):
                $order_by = "likes_count {$order}";
                break;
            case ($_GET['sort'] === 'date'):
                $order_by = "p.creation_date {$order}";
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
         " . (($is_typed) ? 'WHERE t.id = ?' : '') . "
         GROUP BY " . (($is_sorted) ? 'p.id' : 'p.id, p.views_count') . "
         ORDER BY " . (($is_sorted) ? $order_by : 'p.views_count DESC') . "
         LIMIT {$limit}
         OFFSET ?;";

    if ($is_typed) {
        $data = sql_get_many($db, $sql, [$_GET['post-type'], $offset], 'ss');
    } else {
        $data = sql_get_many($db, $sql, $offset);
    }

    return $data;
}

function get_pages_count ($db, $is_typed = false) {

    if ($is_typed) {
        return current(sql_get_single($db, '
        SELECT COUNT(*)
        FROM posts
        JOIN types t ON t.id = posts.type_id
        WHERE t.id = ?;',
        $_GET['post-type']));
    }

    return current(sql_get_single($db, 'SELECT COUNT(*) FROM posts'));
}
