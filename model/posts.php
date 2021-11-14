<?php

function getPostById($db, $id)
{
    return sqlGetSingle($db, '
        SELECT p.id,
               title,
               content,
               cite_author,
               views_count,
               author_id,
               u.name AS author,
               u.avatar_name AS avatar,
               u.registration_date AS author_reg_date,
               t.class_name AS type,
               (SELECT COUNT(post_id)
                FROM likes l
                WHERE p.id = l.post_id) AS likes_count,
               (SELECT COUNT(post_id)
                FROM comments c
                WHERE p.id = c.post_id) AS comments_count,
               (SELECT COUNT(author_id)
                FROM posts p
                WHERE p.author_id = u.id) AS publications_count,
               (SELECT COUNT(user_id)
                FROM subscriptions s
                WHERE s.user_id = u.id) AS subscriptions_count
        FROM posts p
                 JOIN users u ON author_id = u.id
                 JOIN types t ON type_id = t.id
        WHERE p.id = ?;',
        [$id]);
}

function getPosts($db, $offset, $postType = '', $sort = '', $sortDirection = '', $limit = 6)
{
    // QSTN валидация параметров перед запросом http://readme.loc/?sort=popularity&direction=gnflg
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

    //  TODO получить посты пользователей на которых подписан

    $sql = "SELECT p.*,
             u.name AS author,
             u.avatar_name AS avatar,
             t.name AS type_name,
             t.class_name AS type,
             (SELECT COUNT(post_id)
             FROM likes l
             WHERE p.id = l.post_id) AS likes_count,
             (SELECT COUNT(post_id)
             FROM comments c
             WHERE p.id = c.post_id) AS comments_count
         FROM posts p
             JOIN users u ON p.author_id = u.id
             JOIN types t ON p.type_id = t.id
         " . ($postType ? 'WHERE t.id = ?' : '') . "
         ORDER BY " . ($orderBy ?? 'p.views_count DESC') . "
         LIMIT ?
         OFFSET ?;";

    $data = $postType ? [$postType, $limit, $offset] : [$limit, $offset];

    return sqlGetMany($db, $sql, $data);
}

function getPostsForFeed($db, int $id, string $postType = null)
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
                   (SELECT COUNT(post_id)
                    FROM likes l
                    WHERE p.id = l.post_id) AS likes_count,
                   (SELECT COUNT(post_id)
                    FROM comments c
                    WHERE p.id = c.post_id) AS comments_count,
                    (SELECT COUNT(original_post_id)
                    FROM posts
                    WHERE original_post_id = p.id) AS reposts_count
            FROM posts p
                     JOIN subscriptions s ON s.user_id = p.author_id
                     JOIN users u ON p.author_id = u.id
                     JOIN types t ON p.type_id = t.id
            WHERE s.follower_id = ?" . ($postType ? '&& t.id = ?' : '') . "
            ORDER BY p.creation_date DESC;";

    $data = $postType ? [$id, $postType] : [$id];

    return sqlGetMany($db, $sql, $data);
}

function getUserPosts($db, array $data)
{
    $sql = "SELECT p.*,
                u.name AS author,
                t.name AS type_name,
                t.class_name AS type,
                (SELECT COUNT(post_id)
                FROM likes l
                WHERE p.id = l.post_id) AS likes_count,
                (SELECT COUNT(post_id)
                FROM comments c
                WHERE p.id = c.post_id) AS comments_count,
                (SELECT COUNT(original_post_id)
                FROM posts
                WHERE original_post_id = p.id) AS reposts_count
            FROM posts p
                JOIN users u ON p.author_id = u.id
                JOIN types t ON p.type_id = t.id
            WHERE author_id = ?
            ORDER BY creation_date DESC";

    return sqlGetMany($db, $sql, $data);
}

function getPostsLikedByUsers($db, array $data)
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
            ORDER BY l.date DESC";

    return sqlGetMany($db, $sql, $data);
}

function getSubscribedUsers($db, array $data)
{
    $sql = "SELECT u.id,
                   u.name,
                   u.avatar_name AS avatar,
                   u.registration_date AS date,
                   (SELECT COUNT(author_id)
                    FROM posts p
                    WHERE p.author_id = u.id) AS publications_count,
                   (SELECT COUNT(user_id)
                    FROM subscriptions s
                    WHERE s.user_id = u.id) AS subscriptions_count
            FROM users u
                JOIN subscriptions s ON u.id = s.follower_id
            WHERE s.user_id = ?
            ORDER BY s.id DESC";

    return sqlGetMany($db, $sql, $data);
}

function getPagesCount($db, string $postType = null)
{
    return ($postType)
        ? current(sqlGetSingle($db, '
        SELECT COUNT(*)
        FROM posts
        WHERE type_id = ?;',
            [$postType]))
        : current(sqlGetSingle($db, 'SELECT COUNT(*) FROM posts'));
}

function insertNewPost($db, array $data)
{
    $sql = "INSERT INTO posts (title, type_id, author_id, content, cite_author) VALUES (?, ?, ?, ?, ?)";

    return preparedQuery($db, $sql, [$data['title'], $data['typeId'], $data['authorId'], $data['content'], $data['citeAuthor']]);
}

// qstn есть ли возможность использовать тут if
function incrementViewsCount($db, array $data)
{
    $sql = 'UPDATE posts SET views_count = views_count + 1 WHERE id = ?';

    return preparedQuery($db, $sql, $data);
}

function insertRepost($db, array $data)
{
    $sql = 'INSERT INTO posts (title, type_id, author_id, content, cite_author, original_post_id)
            SELECT title, type_id, ?, content, cite_author, id
            FROM posts
            WHERE id = ?';

    return preparedQuery($db, $sql, $data);
}
