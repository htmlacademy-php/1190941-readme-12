<?php

/**
 * @param mysqli $db
 * @param int $followerID
 * @return array
 */
function checkSubscriptions(mysqli $db, int $followerID): array
{
    $sql = "SELECT * FROM subscriptions WHERE follower_id = ?";

    return sqlGetMany($db, $sql, [$followerID]);
}

/**
 * @param mysqli $db
 * @param int $followerID
 * @param int $userID
 * @return false|mysqli_stmt
 */
function subscribe(mysqli $db, int $followerID, int $userID)
{
    $sql = "INSERT INTO subscriptions (follower_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$followerID, $userID]);
}

/**
 * @param mysqli $db
 * @param int $followerID
 * @param int $userID
 * @return false|mysqli_stmt
 */
function unsubscribe(mysqli $db, int $followerID, int $userID)
{
    $sql = "DELETE FROM subscriptions WHERE follower_id = ? && user_id = ?";

    return preparedQuery($db, $sql, [$followerID, $userID]);
}
