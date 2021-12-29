<?php

/**
 * @param mysqli $db
 * @param int $id
 * @return array
 */
function getPostComments(mysqli $db, int $id): array
{
    return sqlGetMany($db, '
    SELECT comment AS text,
        date AS date,
        u.id AS user_id,
        u.name AS author,
        u.avatar_name AS author_avatar
    FROM comments
        JOIN users u ON comments.author_id = u.id
    WHERE post_id = ?
    ORDER BY date DESC;',
        [$id]);
}

/**
 * @param mysqli $db
 * @param string $comment
 * @param int $postID
 * @param int $authorID
 * @return false|mysqli_stmt
 */
function insertNewComment(mysqli $db, string $comment, int $postID, int $authorID)
{
    $sql = "INSERT INTO comments (comment, post_id, author_id) VALUES (?, ?, ?)";

    return preparedQuery($db, $sql, [$comment, $postID, $authorID]);
}
