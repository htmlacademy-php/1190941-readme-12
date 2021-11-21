<?php

function selectUserByEmail($db, array $data)
{
    $sql = "SELECT * from users WHERE email = ?";

    return sqlGetSingle($db, $sql, $data);
}

function selectUserIdByName($db, array $data)
{
    $sql = "SELECT id from users WHERE name = ?";

    return sqlGetSingle($db, $sql, $data);
}

function getUser($db, array $data)
{
    $sql = "SELECT * from users WHERE email = ?";

    return sqlGetSingle($db, $sql, $data);
}

function selectUser($db, string $where, array $fields, array $data)
{
    $fields = implode(', ', $fields);
    $sql = "SELECT {$fields} FROM users WHERE {$where} = ?";

    return sqlGetSingle($db, $sql, $data);
}

function getProfileData($db, string $userId)
{
    $sql = "SELECT name,
                   avatar_name AS avatar,
                   registration_date AS date,
                   (SELECT COUNT(author_id)
                    FROM posts p
                    WHERE p.author_id = u.id) AS publications_count,
                   (SELECT COUNT(user_id)
                    FROM subscriptions s
                    WHERE s.user_id = u.id) AS subscriptions_count
            FROM users u
            WHERE id = ?";

    return sqlGetSingle($db, $sql, [$userId]);
}

function createUser($db, array $data)
{
    $sql = "INSERT INTO users (name, email, password, avatar_name) VALUES (?, ?, ?, ?)";

    // TODO Разобраться почему не $data
    return preparedQuery($db, $sql, [$data['login'], $data['email'], $data['password'], $data['avatar']]);
}
