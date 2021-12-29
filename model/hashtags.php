<?php

/**
 * @param mysqli $db
 * @param int $id
 * @return array
 */
function getPostTags(mysqli $db, int $id): array
{
    return sqlGetMany($db,
        'SELECT h.name
             FROM hashtags h
                 JOIN post_tags pt ON h.id = pt.hashtag_id
             WHERE pt.post_id = ?;',
        [$id]);
}

/**
 * @param mysqli $db
 * @param string $hashtag
 * @return array|false|null
 */
function selectTag(mysqli $db, string $hashtag)
{
    $sql = "SELECT id, name FROM hashtags WHERE name = ?";

    return sqlGetSingle($db, $sql, [$hashtag]);
}

/**
 * @param mysqli $db
 * @param string $hashtag
 * @return false|mysqli_stmt
 */
function insertTag(mysqli $db, string $hashtag)
{
    $sql = "INSERT INTO hashtags (name) VALUES (?)";

    return preparedQuery($db, $sql, [$hashtag]);
}

/**
 * @param mysqli $db
 * @param int $hashtagID
 * @param int $postID
 * @return array|false|null
 */
function selectTagToPost(mysqli $db, int $hashtagID, int $postID)
{
    $sql = "SELECT hashtag_id, post_id FROM post_tags WHERE hashtag_id = ? && post_id = ?";

    return sqlGetSingle($db, $sql, [$hashtagID, $postID]);
}

/**
 * @param mysqli $db
 * @param int $hashtagID
 * @param int $postID
 * @return false|mysqli_stmt
 */
function setTagToPost(mysqli $db, int $hashtagID, int $postID)
{
    $sql = "INSERT INTO post_tags (hashtag_id, post_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$hashtagID, $postID]);
}
