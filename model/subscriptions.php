<?php

function checkSubscriptions(mysqli $db, int $followerID)
{
    $sql = "SELECT * FROM subscriptions WHERE follower_id = ?";

    return sqlGetMany($db, $sql, [$followerID]);
}

function subscribe(mysqli $db, int $followerID, int $userID)
{
    $sql = "INSERT INTO subscriptions (follower_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$followerID, $userID]);
}

function unsubscribe(mysqli $db, int $followerID, int $userID)
{
    $sql = "DELETE FROM subscriptions WHERE follower_id = ? && user_id = ?";

    return preparedQuery($db, $sql, [$followerID, $userID]);
}
