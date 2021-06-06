<?php

function get_post_by_id ($db, $id) {
    return sql_get_single($db, '
        SELECT p.id, title,
               text_content AS text,
               cite_author,
               img_name AS photo,
               youtube_link,
               link,
               views_count,
               repost,
               u.name AS author,
               u.avatar_name AS avatar,
               u.registration_date AS author_reg_date,
               t.class_name AS type,
               (SELECT COUNT(post_id)
                FROM likes l
                WHERE p.id = l.post_id) AS likes_count,
               (SELECT COUNT(post_id)
                FROM comments c
                WHERE p.id = c.post_id) AS comments_count,
               (SELECT COUNT(author_id)
                FROM posts p
                WHERE p.author_id = u.id) AS publications_count
        FROM posts p
                 JOIN users u ON author_id = u.id
                 JOIN types t ON type_id = t.id
        WHERE p.id = ?;',
        [$id]);
}

function get_posts ($db, $offset, $post_type = '', $sort = '', $sort_direction = '', $limit = 6) {

    // TODO валидация параметров перед запросом http://readme.loc/?sort=popularity&direction=gnflg
    $direction = $sort_direction ?? 'desc';

    if ($direction !== null && !in_array($direction, ['desc', 'asc'])) {
        throw new InvalidArgumentException("Invalid Order By direction value");
    }

    switch ($sort) {
        case 'popularity':
            $order_by = "likes_count $direction, comments_count $direction, p.views_count $direction";
            break;
        case 'likes':
            $order_by = "likes_count $direction";
            break;
        case 'date':
            $order_by = "p.creation_date $direction";
            break;
        case null:
            $order_by = 'p.views_count DESC';
            break;
        default:
            throw new InvalidArgumentException("Invalid Order By direction value");
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
         " . ($post_type ? 'WHERE t.id = ?' : '') . "
         ORDER BY $order_by
         LIMIT ?
         OFFSET ?;";

    return ($post_type)
        ? sql_get_many($db, $sql, [$post_type, $limit, $offset])
        : sql_get_many($db, $sql, [$limit, $offset]);
}

function get_pages_count ($db, $post_type = '') {
    return ($post_type)
        ? current(sql_get_single($db, '
        SELECT COUNT(*)
        FROM posts
        WHERE type_id = ?;',
        [$post_type]))
        : current(sql_get_single($db, 'SELECT COUNT(*) FROM posts'));
}
