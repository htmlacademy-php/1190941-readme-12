<?php

function getPostTags ($db, $id)
{
    return sqlGetMany($db,
        'SELECT h.name
             FROM hashtags h
                 JOIN post_tags pt ON h.id = pt.hashtag_id
             WHERE pt.post_id = ?;',
        [$id]);
}

function selectTag ($db, array $hashtag)
{
    $sql = "SELECT id, name FROM hashtags WHERE name = ?";

    return sqlGetSingle($db, $sql, $hashtag);
}

function insertTag ($db, array $hashtag)
{
    $sql = "INSERT INTO hashtags (name) VALUES (?)";

    return preparedQuery($db, $sql, $hashtag);
}

function selectTagToPost ($db, array $data)
{
    $sql = "SELECT hashtag_id, post_id FROM post_tags WHERE hashtag_id = ? && post_id = ?";

    return sqlGetSingle($db, $sql, $data);
}

function setTagToPost ($db, array $data)
{
    $sql = "INSERT INTO post_tags (hashtag_id, post_id) VALUES (?, ?)";

    return preparedQuery($db, $sql, $data);
}
