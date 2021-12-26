<?php

function getPostById(mysqli $db, int $id)
{
    return sqlGetSingle($db, 'SELECT p.id,
                                       p.title,
                                       p.content,
                                       p.cite_author,
                                       p.views_count,
                                       p.author_id,
                                       u.name AS author,
                                       u.avatar_name AS avatar,
                                       u.registration_date AS author_reg_date,
                                       t.class_name AS type,
                                       COUNT(DISTINCT l.id) AS likes_count,
                                       COUNT(DISTINCT c.id) AS comments_count,
                                       COUNT(DISTINCT p2.id) AS publications_count,
                                       COUNT(DISTINCT s.id) AS subscriptions_count
                                FROM posts p
                                        JOIN users u ON author_id = u.id
                                        JOIN types t ON type_id = t.id
                                        LEFT JOIN likes l ON p.id = l.post_id
                                        LEFT JOIN comments c ON p.id = c.post_id
                                        JOIN posts p2 ON p2.author_id = u.id
                                        LEFT JOIN subscriptions s ON s.user_id = u.id
                                WHERE p.id = ?
                                GROUP BY p.id;',
        [$id]);
}

function getPosts(
    mysqli $db,
    int $offset,
    ?string $postType = '',
    ?string $sort = '',
    ?string $sortDirection = '',
    int $limit = 6
) {
    $direction = $sortDirection ?? 'desc';

    switch ($sort) {
        case 'popularity':
            $orderBy = "likes_count $direction, comments_count $direction, p.views_count $direction";
            break;
        case 'likes':
            $orderBy = "likes_count $direction";
            break;
        case 'date':
            $orderBy = "p.creation_date $direction";
            break;
    }

    $sql = "SELECT p.*,
             u.name AS author,
             u.avatar_name AS avatar,
             t.name AS type_name,
             t.class_name AS type,
             COUNT(DISTINCT l.id) AS likes_count,
             COUNT(DISTINCT c.id) AS comments_count
         FROM posts p
             JOIN users u ON p.author_id = u.id
             JOIN types t ON p.type_id = t.id
             LEFT JOIN likes l ON p.id = l.post_id
             LEFT JOIN comments c ON p.id = c.post_id
         " . ($postType ? 'WHERE t.id = ?' : '') . "
         GROUP BY p.id
         ORDER BY " . ($orderBy ?? 'p.views_count DESC') . "
         LIMIT ?
         OFFSET ?;";

    $data = $postType ? [$postType, $limit, $offset] : [$limit, $offset];

    return sqlGetMany($db, $sql, $data);
}

function getPostsForFeed(mysqli $db, int $id, ?string $postType = null)
{
    $sql = "SELECT p.id,
                   p.title,
                   p.creation_date,
                   p.author_id,
                   p.content,
                   p.cite_author,
                   u.name AS author,
                   u.avatar_name AS avatar,
                   t.name AS type_name,
                   t.class_name AS type,
                   COUNT(DISTINCT l.id) AS likes_count,
                   COUNT(DISTINCT c.id) AS comments_count,
                   COUNT(DISTINCT p2.id) AS reposts_count
            FROM posts p
                     JOIN subscriptions s ON s.user_id = p.author_id
                     JOIN users u ON p.author_id = u.id
                     JOIN types t ON p.type_id = t.id
                     LEFT JOIN likes l ON p.id = l.post_id
                     LEFT JOIN comments c ON p.id = c.post_id
                     LEFT JOIN posts p2 ON p2.original_post_id = p.id
            WHERE s.follower_id = ?" . ($postType ? '&& t.id = ?' : '') . "
            GROUP BY p.id
            ORDER BY p.creation_date DESC;";

    $data = $postType ? [$id, $postType] : [$id];

    return sqlGetMany($db, $sql, $data);
}

function getUserPosts(mysqli $db, int $authorID)
{
    $sql = "SELECT p.*,
                   u.name AS author,
                   t.name AS type_name,
                   t.class_name AS type,
                   COUNT(DISTINCT l.id) AS likes_count,
                   COUNT(DISTINCT c.id) AS comments_count,
                   COUNT(DISTINCT p2.id) AS reposts_count
            FROM posts p
                     JOIN users u ON p.author_id = u.id
                     JOIN types t ON p.type_id = t.id
                     LEFT JOIN likes l ON p.id = l.post_id
                     LEFT JOIN comments c ON p.id = c.post_id
                     LEFT JOIN posts p2 ON p2.original_post_id = p.id
            WHERE p.author_id = ?
            GROUP BY p.id
            ORDER BY p.creation_date DESC;";

    return sqlGetMany($db, $sql, [$authorID]);
}

function getLikedPostsByAuthor(mysqli $db, int $authorID)
{
    $sql = "SELECT p.id,
                   p.content,
                   l.date,
                   u.id AS user_id,
                   u.name AS user_name,
                   u.avatar_name AS avatar,
                   t.class_name AS type
            FROM posts p
                JOIN likes l ON p.id = l.post_id
                JOIN users u ON l.user_id = u.id
                JOIN types t ON p.type_id = t.id
            WHERE author_id = ?
            ORDER BY l.date DESC;";

    return sqlGetMany($db, $sql, [$authorID]);
}

function getPagesCount(mysqli $db, ?string $postType = null)
{
    return ($postType)
        ? current(sqlGetSingle($db, '
        SELECT COUNT(*)
        FROM posts
        WHERE type_id = ?;',
            [$postType]))
        : current(sqlGetSingle($db, 'SELECT COUNT(*) FROM posts'));
}

function insertNewPost(
    mysqli $db,
    string $title,
    int $typeID,
    int $authorID,
    string $content,
    ?string $citeAuthor = null
) {
    $sql = "INSERT INTO posts (title, type_id, author_id, content, cite_author) VALUES (?, ?, ?, ?, ?)";

    return preparedQuery($db, $sql, [$title, $typeID, $authorID, $content, $citeAuthor]);
}

function incrementViewsCount(mysqli $db, int $id)
{
    $sql = 'UPDATE posts SET views_count = views_count + 1 WHERE id = ?';

    return preparedQuery($db, $sql, [$id]);
}

function insertRepost(mysqli $db, int $authorID, int $id)
{
    $sql = 'INSERT INTO posts (title, type_id, author_id, content, cite_author, original_post_id)
            SELECT title, type_id, ?, content, cite_author, id
            FROM posts
            WHERE id = ?';

    return preparedQuery($db, $sql, [$authorID, $id]);
}

function searchPosts(mysqli $db, string $queryText, ?string $type = null)
{
    $sql = 'SELECT p.id,
                   p.title,
                   p.creation_date,
                   p.author_id,
                   p.content,
                   p.cite_author,
                   u.name AS author,
                   u.avatar_name AS avatar,
                   t.name AS type_name,
                   t.class_name AS type,
                   COUNT(DISTINCT l.id) AS likes_count,
                   COUNT(DISTINCT c.id) AS comments_count,
                   COUNT(DISTINCT p2.id) AS reposts_count
            FROM posts p
                     LEFT JOIN subscriptions s ON s.user_id = p.author_id
                     JOIN users u ON p.author_id = u.id
                     JOIN types t ON p.type_id = t.id
                     LEFT JOIN likes l ON p.id = l.post_id
                     LEFT JOIN comments c ON p.id = c.post_id
                     LEFT JOIN posts p2 ON p2.original_post_id = p.id
                     ' . ($type === 'hashtag'
            ? 'JOIN post_tags pt ON p.id = pt.post_id JOIN hashtags h ON pt.hashtag_id = h.id '
            : '') .
        'WHERE ' . ($type === 'hashtag'
            ? 'h.name = ? GROUP BY p.id ORDER BY p.creation_date DESC'
            : ' MATCH(p.title, p.content) AGAINST(?) GROUP BY p.id');

    return sqlGetMany($db, $sql, [$queryText]);
}
