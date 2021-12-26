<?php

function getChats(mysqli $db, int $senderID, int $recipientID): array
{
    $sql = "SELECT m.*,
                   u.avatar_name,
                   u.name
            FROM messages m
                     JOIN users u ON u.id = m.recipient_id
            WHERE m.id IN
                  (
                      SELECT MAX(id)
                      FROM messages
                      WHERE sender_id = ?
                         OR recipient_id = ?
                      GROUP BY IF(sender_id < recipient_id,
                                  CONCAT(sender_id, ':', recipient_id),
                                  CONCAT(recipient_id, ':', sender_id))
                  );";

    return sqlGetMany($db, $sql, [$senderID, $recipientID]);
}

function getChat(mysqli $db, int $senderID, int $recipientID): array
{
    $sql = 'SELECT m.message,
                   m.date,
                   m.sender_id AS id,
                   u.name AS name,
                   u.avatar_name AS avatar
            FROM messages m
                JOIN users u ON u.id = m.sender_id
            WHERE m.sender_id = ? && m.recipient_id = ? || m.sender_id = ? && m.recipient_id = ?
            ORDER BY m.date;';

    return sqlGetMany($db, $sql, [$senderID, $recipientID, $recipientID, $senderID]);
}

function sendMessage(mysqli $db, string $message, int $recipientID, int $senderID)
{
    $sql = 'INSERT INTO messages (message, recipient_id, sender_id) VALUES (?, ?, ?);';

    return preparedQuery($db, $sql, [$message, $recipientID, $senderID]);
}
