<?php

function getPostTags(mysqli $db, int $id)
{
    return sqlGetMany($db,
        'SELECT h.name
             FROM hashtags h
                 JOIN post_tags pt ON h.id = pt.hashtag_id
             WHERE pt.post_id = ?;',
        [$id]);
}

function selectTag(mysqli $db, string $hashtag)
{
    $sql = "SELECT id, name FROM hashtags WHERE name = ?";

    return sqlGetSingle($db, $sql, [$hashtag]);
}

function insertTag(mysqli $db, string $hashtag)
{
    $sql = "INSERT INTO hashtags (name) VALUES (?)";

    return preparedQuery($db, $sql, [$hashtag]);
}

function selectTagToPost(mysqli $db, int $hashtagID, int $postID)
{
    $sql = "SELECT hashtag_id, post_id FROM post_tags WHERE hashtag_id = ? && post_id = ?";

    return sqlGetSingle($db, $sql, [$hashtagID, $postID]);
}

function setTagToPost(mysqli $db, int $hashtagID, int $postID)
{
    $sql = "INSERT INTO post_tags (hashtag_id, post_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, [$hashtagID, $postID]);
}
