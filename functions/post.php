<?php

function get_post_by_id ($db) {
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
        COUNT(l.post_id) AS likes_count,
        COUNT(DISTINCT c.post_id) AS comments_count,
        (SELECT COUNT(author_id)
            FROM posts p
                JOIN users u on p.author_id = u.id
            WHERE author_id = (SELECT author_id
                                FROM posts
                                WHERE id = ?))
                AS publications_count
        FROM posts p
            JOIN users u ON author_id = u.id
            JOIN types t ON type_id = t.id
            LEFT JOIN likes l ON p.id = l.post_id
            LEFT JOIN comments c ON p.id = c.post_id
        WHERE p.id = ?
        GROUP BY p.id, c.id;',
        array($_GET['id'], $_GET['id']), 'ss');
}

function get_post_comments ($db) {
    return sql_get_many($db, '
    SELECT comment AS text,
        date AS date,
        u.name AS author,
        u.avatar_name AS author_avatar
    FROM comments
        JOIN users u on comments.author_id = u.id
    WHERE post_id = ?
    ORDER BY date DESC;',
    $_GET['id']);
}
