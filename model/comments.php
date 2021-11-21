<?php

function getPostComments ($db, $id)
{
    return sqlGetMany($db, '
    SELECT comment AS text,
        date AS date,
        u.id AS user_id,
        u.name AS author,
        u.avatar_name AS author_avatar
    FROM comments
        JOIN users u on comments.author_id = u.id
    WHERE post_id = ?
    ORDER BY date DESC;',
        [$id]);
}

function insertNewComment($db, array $data)
{
    $sql = "INSERT INTO comments (comment, post_id, author_id) VALUES (?, ?, ?)";

    return preparedQuery($db, $sql, $data);
}
