<?php

function insertLike($db, array $data)
{
    $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, $data);
}

function deleteLike($db, array $data)
{
    $sql = "DELETE FROM likes WHERE post_id = ? && user_id = ?";

    return preparedQuery($db, $sql, $data);
}

function getLikes($db, array $data)
{
    $sql = "SELECT * FROM likes WHERE user_id = ?";

    return sqlGetMany($db, $sql, $data);
}
