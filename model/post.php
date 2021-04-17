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

function get_post_comments ($db, $id) {
    return sql_get_many($db, '
    SELECT comment AS text,
        date AS date,
        u.name AS author,
        u.avatar_name AS author_avatar
    FROM comments
        JOIN users u on comments.author_id = u.id
    WHERE post_id = ?
    ORDER BY date DESC;',
    [$id]);
}
