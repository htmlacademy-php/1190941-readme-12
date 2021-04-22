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

function get_posts ($db, $offset, $post_type = '', $sort = '', $sort_order = '', $limit = 6) {
    $order = $sort_order ?? 'desc';

    switch ($sort) {
        case 'popularity':
            $order_by = "likes_count $order, comments_count $order, p.views_count $order";
            break;
        case 'likes':
            $order_by = "likes_count $order";
            break;
        case 'date':
            $order_by = "p.creation_date $order";
            break;
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
         ORDER BY " . ($order_by ?? 'p.views_count DESC') . "
         LIMIT ?
         OFFSET ?;";

    return ($post_type)
        ? sql_get_many($db, $sql, [$post_type, $limit, $offset])
        : sql_get_many($db, $sql, [$limit, $offset]);
}

function get_pages_count ($db, $post_type = []) {
    return ($post_type)
        ? current(sql_get_single($db, '
        SELECT COUNT(*)
        FROM posts
        JOIN types t ON t.id = posts.type_id
        WHERE t.id = ?;',
        [$post_type]))
        : current(sql_get_single($db, 'SELECT COUNT(*) FROM posts'));
}
