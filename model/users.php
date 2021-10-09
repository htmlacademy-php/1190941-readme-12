<?php

function selectUserEmail ($db, array $data)
{
    $sql = "SELECT email from users WHERE email = ?";

    return sqlGetSingle($db, $sql, $data);
}

function getUser ($db, array $data)
{
    $sql = "SELECT * from users WHERE email = ?";

    return sqlGetSingle($db, $sql, $data);
}

function insertUser ($db, array $data)
{
    $sql = "INSERT INTO users (name, email, password, avatar_name) VALUES (?, ?, ?, ?)";

    return preparedQuery($db, $sql, [$data['login'], $data['email'], $data['password'], $data['avatar']]);
}
