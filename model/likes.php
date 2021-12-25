<?php

function insertLike(mysqli $db, int $postID, int $userID)
{
    $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$postID, $userID]);
}

function deleteLike(mysqli $db, int $postID, int $userID)
{
    $sql = "DELETE FROM likes WHERE post_id = ? && user_id = ?";

    return preparedQuery($db, $sql, [$postID, $userID]);
}

function getLikes(mysqli $db, int $userID)
{
    $sql = "SELECT * FROM likes WHERE user_id = ?";

    return sqlGetMany($db, $sql, [$userID]);
}
