USE readme_db;

INSERT INTO types (name, class_name)
VALUES ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Картинка', 'photo'),
       ('Видео', 'video'),
       ('Ссылка', 'link');

INSERT INTO users (name, email, password, avatar_path)
VALUES ('Лариса', 'larisa@readme.loc', '123', 'img/userpic-larisa-small.jpg'),
       ('Владик', 'vladik@readme.loc', '123', 'img/userpic.jpg'),
       ('Виктор', 'viktor@readme.loc', '123', 'img/userpic-mark.jpg');

INSERT INTO posts (title, type_id, author_id, text_content, cite_author)
VALUES ('Цитата', 2, 1, 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор');

INSERT INTO posts (title, type_id, author_id, text_content)
VALUES ('Игра престолов', 1, 2, 'Не могу дождаться начала финального сезона своего любимого сериала!');

INSERT INTO posts (title, type_id, author_id, img_path)
VALUES ('Наконец, обработал фотки!', 3, 3, 'img/rock-medium.jpg');

INSERT INTO posts (title, type_id, author_id, img_path)
VALUES ('Моя мечта', 3, 1, 'img/coast-medium.jpg');

INSERT INTO posts (title, type_id, author_id, link)
VALUES ('Лучшие курсы', 5, 2, 'www.htmlacademy.ru');

INSERT INTO comments (comment, post_id, author_id)
VALUES ('И я!', 2, 1),
       ('Круто!', 3, 3),
       ('Согласен!', 5, 2);

INSERT INTO likes (post_id, user_id)
VALUES (1, 2),
       (2, 1),
       (3, 1),
       (3, 2),
       (4, 3),
       (4, 2),
       (5, 3);

/* Добавить лайк к посту; */
INSERT INTO likes (post_id, user_id) VALUES (2, 3);

/* Подписаться на пользователя. */
INSERT INTO subscriptions (follower_id, user_id) VALUES (1, 2);

/* Получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента; */
SELECT title,
       u.name AS author,
       t.name AS post_type,
       COUNT(l.post_id) AS likes_count
FROM posts p
       JOIN users u ON p.author_id = u.id
       JOIN types t ON p.type_id = t.id
       JOIN likes l on p.id = l.post_id
GROUP BY l.post_id
ORDER BY likes_count DESC;

/* Получить список постов для конкретного пользователя; */
SELECT * FROM posts WHERE author_id = 2;

/* Получить список комментариев для одного поста, в комментариях должен быть логин пользователя; */
SELECT comment, u.name FROM comments c JOIN users u ON c.author_id = u.id WHERE post_id = 3;
