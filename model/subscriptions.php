<?php

function checkSubscription($db, array $data)
{
    $sql = "SELECT * FROM subscriptions WHERE follower_id = ? && user_id = ?";

    return sqlGetMany($db, $sql, $data);
}

function subscribe($db, array $data)
{
    $sql = "INSERT INTO subscriptions (follower_id, user_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, $data);
}

function unsubscribe($db, array $data)
{
    $sql = "DELETE FROM subscriptions WHERE follower_id = ? && user_id = ?";

    return preparedQuery($db, $sql, $data);
}
