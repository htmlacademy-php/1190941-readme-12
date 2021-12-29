<?php

/**
 * @param mysqli $db
 * @param int $postID
 * @param int $userID
 * @return false|mysqli_stmt
 */
function insertLike(mysqli $db, int $postID, int $userID)
{
    $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$postID, $userID]);
}

/**
 * @param mysqli $db
 * @param int $postID
 * @param int $userID
 * @return false|mysqli_stmt
 */
function deleteLike(mysqli $db, int $postID, int $userID)
{
    $sql = "DELETE FROM likes WHERE post_id = ? && user_id = ?";

    return preparedQuery($db, $sql, [$postID, $userID]);
}

/**
 * @param mysqli $db
 * @param int $userID
 * @return array
 */
function getLikes(mysqli $db, int $userID): array
{
    $sql = "SELECT * FROM likes WHERE user_id = ?";

    return sqlGetMany($db, $sql, [$userID]);
}
