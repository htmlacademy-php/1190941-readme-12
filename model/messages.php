<?php

/**
 * @param mysqli $db
 * @param int $senderID
 * @param int $recipientID
 * @return array
 */
function getChats(mysqli $db, int $senderID, int $recipientID): array
{
    $sql = "SELECT m.id,
                   m.message,
                   m.date,
                   IF(m.recipient_id = ?, m.sender_id, m.recipient_id) AS user_id,
                   u.avatar_name,
                   u.name
            FROM messages m
                     JOIN users u ON u.id = IF(m.recipient_id = ?, m.sender_id, m.recipient_id)
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

    return sqlGetMany($db, $sql, [$recipientID, $recipientID, $senderID, $recipientID]);
}

/**
 * @param mysqli $db
 * @param int $senderID
 * @param int $recipientID
 * @return array
 */
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

/**
 * @param mysqli $db
 * @param string $message
 * @param int $recipientID
 * @param int $senderID
 * @return false|mysqli_stmt
 */
function sendMessage(mysqli $db, string $message, int $recipientID, int $senderID)
{
    $sql = 'INSERT INTO messages (message, recipient_id, sender_id) VALUES (?, ?, ?);';

    return preparedQuery($db, $sql, [$message, $recipientID, $senderID]);
}
