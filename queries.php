<?php

return [
    'post' => [
        'types' => 'SELECT * FROM types;',
        'single' => 'SELECT p.id, title,
                           text_content AS text,
                           cite_author,
                           img_name AS photo,
                           youtube_link,
                           link,
                           views_count,
                           repost,
                           u.name AS author,
                           u.avatar_name AS avatar,
                           u.registration_date AS author_reg_date,
                           t.class_name AS type,
                           COUNT(l.post_id) AS likes_count,
                           COUNT(DISTINCT c.post_id) AS comments_count,
                           (SELECT COUNT(author_id)
                            FROM posts p
                                JOIN users u on p.author_id = u.id
                            WHERE author_id = (SELECT author_id
                                                    FROM posts
                                                    WHERE id = ?)) AS publications_count
                    FROM posts p
                             JOIN users u ON author_id = u.id
                             JOIN types t ON type_id = t.id
                             LEFT JOIN likes l ON p.id = l.post_id
                             LEFT JOIN comments c ON p.id = c.post_id
                    WHERE p.id = ?
                    GROUP BY p.id, c.id;',
        'comments' => 'SELECT comment AS text,
                               date AS date,
                               u.name AS author,
                               u.avatar_name AS author_avatar
                        FROM comments
                            JOIN users u on comments.author_id = u.id
                        WHERE post_id = ?
                        ORDER BY date DESC;',
    ],
    'posts' => [
        'default' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            GROUP BY p.id
                            ORDER BY p.views_count DESC;',
        'by_types' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            WHERE t.id = ?
                            GROUP BY p.id
                            ORDER BY p.views_count DESC;',
        'popularity_desc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                GROUP BY p.id
                                ORDER BY likes_count DESC, comments_count DESC, p.views_count DESC;',
        'popularity_asc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                GROUP BY p.id
                                ORDER BY likes_count ASC, comments_count ASC, p.views_count ASC;',
        'likes_desc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                GROUP BY p.id
                                ORDER BY likes_count DESC;',
        'likes_asc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            GROUP BY p.id
                            ORDER BY likes_count ASC;',
        'date_desc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            GROUP BY p.id
                            ORDER BY p.creation_date DESC;',
        'date_asc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            GROUP BY p.id
                            ORDER BY p.creation_date ASC;',
        'type_popularity_desc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                WHERE t.id = ?
                                GROUP BY p.id
                                ORDER BY likes_count DESC, comments_count DESC, p.views_count DESC;',
        'type_popularity_asc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                WHERE t.id = ?
                                GROUP BY p.id
                                ORDER BY likes_count ASC, comments_count ASC, p.views_count ASC;',
        'type_likes_desc' => 'SELECT p.*,
                                    u.name AS author,
                                    u.avatar_name AS avatar,
                                    t.name AS type_name,
                                    t.class_name AS type,
                                    COUNT(l.post_id) AS likes_count,
                                    COUNT(DISTINCT c.post_id) AS comments_count
                                FROM posts p
                                    JOIN users u ON p.author_id = u.id
                                    JOIN types t ON p.type_id = t.id
                                    LEFT JOIN likes l ON p.id = l.post_id
                                    LEFT JOIN comments c ON p.id = c.post_id
                                WHERE t.id = ?
                                GROUP BY p.id
                                ORDER BY likes_count DESC;',
        'type_likes_asc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            WHERE t.id = ?
                            GROUP BY p.id
                            ORDER BY likes_count ASC;',
        'type_date_desc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            WHERE t.id = ?
                            GROUP BY p.id
                            ORDER BY p.creation_date DESC;',
        'type_date_asc' => 'SELECT p.*,
                                u.name AS author,
                                u.avatar_name AS avatar,
                                t.name AS type_name,
                                t.class_name AS type,
                                COUNT(l.post_id) AS likes_count,
                                COUNT(DISTINCT c.post_id) AS comments_count
                            FROM posts p
                                JOIN users u ON p.author_id = u.id
                                JOIN types t ON p.type_id = t.id
                                LEFT JOIN likes l ON p.id = l.post_id
                                LEFT JOIN comments c ON p.id = c.post_id
                            WHERE t.id = ?
                            GROUP BY p.id
                            ORDER BY p.creation_date ASC;',
    ],
];
