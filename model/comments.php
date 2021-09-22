<?php

function getPostComments ($db, $id)
{
    return sqlGetMany($db, '
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
