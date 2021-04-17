<?php

function set_page_link ($is_prev, $params): string {
    $post_type = $params['post-type'] ?? '';
    $page = $params['page'] ?? '';

    if ($is_prev) {
        if ($params['page'] === '2') {
            $page_link = ($post_type) ? '?' . http_build_query(array_filter($params, function ($param) {
                    return $param !== 'page';
                }, ARRAY_FILTER_USE_KEY))
                : '/';
        } else {
            $params['page'] = intval($params['page']) - 1;
            $page_link = '?' . http_build_query($params);
        }
    } else {
        $params['page'] = (!$page) ? '2' : intval($params['page']) + 1;
        $page_link = '?' . http_build_query($params);
    }

    return esc($page_link);
}

function pagination_button_toggle ($total_pages, $params): string {
    $page = $params['page'] ?? '';

    if (intval($page) === $total_pages) {
        return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
            . set_page_link(true, $params)
            . '">Предыдущая страница</a>';
    } elseif (!$page) {
        return '<a class="popular__page-link popular__page-link--next button button--gray" href="'
            . set_page_link(false , $params)
            . '">Следующая страница</a>';
    }

    return '<a class="popular__page-link popular__page-link--prev button button--gray" href="'
        . set_page_link(true, $params)
        . '">Предыдущая страница</a>
            <a class="popular__page-link popular__page-link--next button button--gray" href="'
        . set_page_link(false, $params)
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

function set_type_link ($id): string {
    return esc('?post-type=' . $id);
}

function set_post_link (string $id): string {
    return esc('/post.php?id=' . $id);
}

function set_sort_link ($by, $params): string {
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
