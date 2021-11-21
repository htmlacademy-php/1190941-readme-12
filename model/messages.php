<?php

function getChats($db, $data)
{
    // todo плохо написан запрос, нужно переписать
    $sql = 'SELECT MAX(m.date) AS latest_date,
                   u2.id AS sender_id,
                   u.id AS recipient_id,
                   u2.name AS sender_name,
                   u.name AS recipient_name,
                   u2.avatar_name AS sender_avatar,
                   u.avatar_name AS recipient_avatar
            FROM messages m
                JOIN users u ON u.id = m.recipient_id
                JOIN users u2 ON u2.id = m.sender_id
            WHERE m.recipient_id = ? || m.sender_id = ?
            GROUP BY u.id, u2.id';

    return sqlGetMany($db, $sql, $data);
}

// qstn странно с параметрами получилось, может возможно как-то по другому
function getChat($db, $data)
{
    $sql = 'SELECT m.message,
                   m.date,
                   m.sender_id AS id,
                   u.name AS name,
                   u.avatar_name AS avatar
            FROM messages m
                JOIN users u ON u.id = m.sender_id
            WHERE m.sender_id = ? && m.recipient_id = ? || m.sender_id = ? && m.recipient_id = ?
            ORDER BY m.date ASC;';

    return sqlGetMany($db, $sql, $data);
}

function sendMessage($db, $data)
{
    $sql = 'INSERT INTO messages (message, recipient_id, sender_id) VALUES (?, ?, ?);';

    return preparedQuery($db, $sql, $data);
}
