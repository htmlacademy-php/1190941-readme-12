USE readme_db;

INSERT INTO types (name, class_name)
VALUES ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Картинка', 'photo'),
       ('Видео', 'video'),
       ('Ссылка', 'link');

INSERT INTO users (name, email, password, avatar_name)
VALUES ('Лариса Роговая', 'larisa@readme.loc', '123', 'userpic-larisa.jpg'),
       ('Антон Глуханько', 'anton@readme.loc', '123', 'userpic-anton.jpg'),
       ('Марк Смолов', 'mark@readme.loc', '123', 'userpic-mark.jpg'),
       ('Эльвира Хайпулинова', 'elvira@readme.loc', '123', 'userpic-elvira.jpg'),
       ('Петр Демин', 'petro@readme.loc', '123', 'userpic-petro.jpg'),
       ('Таня Фирсова', 'tanya@readme.loc', '123', 'userpic-tanya.jpg');

INSERT INTO posts (title, type_id, author_id, content, views_count)
VALUES ('Игра престолов', 1, 1, 'Не могу дождаться начала финального сезона своего любимого сериала!', 374),
       ('Игра престолов', 1, 2, 'Не могу дождаться начала финального сезона своего любимого сериала!', 0),
       ('Игра престолов', 1, 3, 'Не могу дождаться начала финального сезона своего любимого сериала!', 81),
       ('Игра престолов', 1, 4, 'Не могу дождаться начала финального сезона своего любимого сериала!', 339),
       ('Игра престолов', 1, 5, 'Не могу дождаться начала финального сезона своего любимого сериала!', 98),
       ('Игра престолов', 1, 6, 'Не могу дождаться начала финального сезона своего любимого сериала!', 62),
       ('Полезный пост про Байкал', 1, 1, 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.', 240),
       ('Полезный пост про Байкал', 1, 4, 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.', 471);

INSERT INTO posts (title, type_id, author_id, content, cite_author, views_count)
VALUES ('Цитата', 2, 1, 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор', 209),
       ('Цитата', 2, 4, 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор', 351),
       ('Цитата', 2, 6, 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор', 25),
       ('Цитата дня', 2, 4, 'Тысячи людей живут без любви, но никто — без воды.', 'Xью Оден', 230),
       ('Цитата дня', 2, 6, 'Тысячи людей живут без любви, но никто — без воды.', 'Xью Оден', 444);

INSERT INTO posts (title, type_id, author_id, content, views_count)
VALUES ('Наконец, обработал фотки!', 3, 2, 'rock.jpg', 308),
       ('Наконец, обработал фотки!', 3, 3, 'rock.jpg', 150),
       ('Наконец, обработал фотки!', 3, 5, 'rock.jpg', 319),
       ('Наконец, обработала фотки!', 3, 1, 'rock.jpg', 251),
       ('Наконец, обработала фотки!', 3, 4, 'rock.jpg', 242),
       ('Наконец, обработала фотки!', 3, 6, 'rock.jpg', 48),
       ('Моя мечта', 3, 2, 'coast.jpg', 89),
       ('Моя мечта', 3, 3, 'coast.jpg', 478),
       ('Моя мечта', 3, 4, 'coast.jpg', 59),
       ('Моя мечта', 3, 5, 'coast.jpg', 482),
       ('Моя мечта', 3, 6, 'coast.jpg', 73);

INSERT INTO posts (title, type_id, author_id, content, views_count)
VALUES ('Тренды веб-разработки в 2021 году', 4, 1, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 176),
       ('Тренды веб-разработки в 2021 году', 4, 2, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 472),
       ('Тренды веб-разработки в 2021 году', 4, 3, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 190),
       ('Тренды веб-разработки в 2021 году', 4, 4, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 125),
       ('Тренды веб-разработки в 2021 году', 4, 5, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 0),
       ('Тренды веб-разработки в 2021 году', 4, 6, 'https://www.youtube.com/watch?v=_YXTLtdGJDM', 311),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 1, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 477),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 2, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 198),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 3, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 473),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 4, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 0),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 5, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 467),
       ('PHP жив? 5 причин учиться бэкенд-разработке', 4, 6, 'https://www.youtube.com/watch?v=mfozVdjyyRs&list=PLQJNT2fdCJnjNdO_mHAoX2wl22ApM-rNx&index=10', 438);

INSERT INTO posts (title, type_id, author_id, content, views_count)
VALUES ('Лучшие курсы', 5, 1, 'www.htmlacademy.ru', 348),
       ('Лучшие курсы', 5, 2, 'www.htmlacademy.ru', 256),
       ('Лучшие курсы', 5, 3, 'www.htmlacademy.ru', 0),
       ('Лучшие курсы', 5, 4, 'www.htmlacademy.ru', 450),
       ('Лучшие курсы', 5, 5, 'www.htmlacademy.ru', 305),
       ('Лучшие курсы', 5, 6, 'www.htmlacademy.ru', 287),
       ('Делюсь с вами ссылочкой', 5, 1, 'www.vitadental.ru', 354),
       ('Делюсь с вами ссылочкой', 5, 2, 'www.vitadental.ru', 0),
       ('Делюсь с вами ссылочкой', 5, 3, 'www.vitadental.ru', 156),
       ('Делюсь с вами ссылочкой', 5, 4, 'www.vitadental.ru', 364),
       ('Делюсь с вами ссылочкой', 5, 5, 'www.vitadental.ru', 138),
       ('Делюсь с вами ссылочкой', 5, 6, 'www.vitadental.ru', 322);

INSERT INTO comments (comment, post_id, author_id)
VALUES ('И я!', 2, 1),
       ('Круто!', 3, 3),
       ('Согласен!', 5, 2),
       ('Красота!!!1!', 23, 1),
       ('Зимой здесь можно кататься на коньках и собачьих упряжках.', 23, 3),
       ('Можно кататься на коньках', 23, 1),
       ('И собачьих упряжках зимой.', 23, 3);

INSERT INTO likes (post_id, user_id)
VALUES (1, 2),
       (2, 1),
       (3, 1),
       (3, 2),
       (4, 3),
       (4, 2),
       (5, 3),
       (10, 1),
       (10, 2),
       (10, 3),
       (10, 5),
       (11, 1),
       (11, 2),
       (11, 3),
       (12, 1),
       (12, 2),
       (12, 4),
       (12, 5),
       (13, 1),
       (13, 2),
       (13, 3),
       (14, 2),
       (14, 4),
       (14, 5),
       (23, 1),
       (23, 3);

INSERT INTO hashtags (name)
VALUES ('nature'),
       ('globe'),
       ('photooftheday'),
       ('canon'),
       ('landscape'),
       ('щикарныйвид');

INSERT INTO post_tags (post_id, hashtag_id)
VALUES (23, 1),
       (23, 2),
       (23, 5),
       (23, 6);

INSERT INTO subscriptions (user_id, follower_id)
VALUES (5, 1),
       (5, 3);

INSERT INTO posts (title, type_id, author_id, original_author_id, content, views_count, repost)
VALUES ('Моя мечта', 3, 1, 5, 'coast.jpg', 348, TRUE),
       ('Моя мечта', 3, 3, 5, 'coast.jpg', 348, TRUE);

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
       LEFT JOIN likes l ON p.id = l.post_id
GROUP BY p.id
ORDER BY likes_count DESC;

/* Получить список постов для конкретного пользователя; */
SELECT * FROM posts WHERE author_id = 2;

/* Получить список комментариев для одного поста, в комментариях должен быть логин пользователя; */
SELECT comment, u.name FROM comments c JOIN users u ON c.author_id = u.id WHERE post_id = 3;
