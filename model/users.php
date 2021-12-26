<?php

function selectUserByEmail(mysqli $db, string $email)
{
    $sql = "SELECT * FROM users WHERE email = ?";

    return sqlGetSingle($db, $sql, [$email]);
}

function selectUser(mysqli $db, string $where, array $fields, array $data)
{
    $fields = implode(', ', $fields);
    $sql = "SELECT {$fields} FROM users WHERE {$where} = ?";

    return sqlGetSingle($db, $sql, $data);
}

function getProfileData(mysqli $db, string $userID)
{
    $sql = "SELECT u.name,
                   u.avatar_name AS avatar,
                   u.registration_date AS date,
                   u.email,
                   COUNT(DISTINCT p.id) AS publications_count,
                   COUNT(DISTINCT s.id) AS subscriptions_count
            FROM users u
                JOIN posts p ON p.author_id = u.id
                LEFT JOIN subscriptions s ON s.user_id = u.id
            WHERE u.id = ?
            GROUP BY u.id";

    return sqlGetSingle($db, $sql, [$userID]);
}

function createUser(mysqli $db, string $name, string $email, string $password, ?string $avatarName)
{
    $sql = "INSERT INTO users (name, email, password, avatar_name) VALUES (?, ?, ?, ?)";

    return preparedQuery($db, $sql, [$name, $email, $password, $avatarName]);
}

function getSubscribedUsers(mysqli $db, int $userID): array
{
    $sql = "SELECT u.id,
                   u.name,
                   u.avatar_name AS avatar,
                   u.registration_date AS date,
                   COUNT(DISTINCT p.id) AS publications_count,
                   COUNT(DISTINCT s2.id) AS subscriptions_count
            FROM users u
                     LEFT JOIN subscriptions s ON u.id = s.follower_id
                     LEFT JOIN subscriptions s2 ON u.id = s2.user_id
                     LEFT JOIN posts p ON u.id = p.author_id
            WHERE s.user_id = ?
            GROUP BY s.id
            ORDER BY s.id DESC;";

    return sqlGetMany($db, $sql, [$userID]);
}

function getSubscribers(mysqli $db, int $userID): array
{
    $sql = "SELECT u.email,
                   u.name
            FROM users u
                JOIN subscriptions s ON u.id = s.follower_id
            WHERE s.user_id = ?;";

    return sqlGetMany($db, $sql, [$userID]);
}
