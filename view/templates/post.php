<?php
/**
 * @var array $post
 * @var array $comments
 * @var array $hashtags
 * @var array $queryString
 * @var bool $subscribed
 * @var array $userData
 */
?>

<div class="container">
    <h1 class="page__title page__title--publication"><?= esc($post['title']) ?></h1>
    <section class="post-details">
        <h2 class="visually-hidden">Публикация</h2>

        <div class="post-details__wrapper post-photo">
            <div class="post-details__main-block post post--details">

                <?php if ($post['type'] === 'photo'): ?>
                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                        <img src="uploads/photos/<?= esc($post['content']) ?>" alt="Фото от пользователя <?= esc($post['author']) ?>" width="760" height="507">
                    </div>

                <?php elseif ($post['type'] === 'quote'): ?>
                    <div class="post-details__image-wrapper post-quote">
                        <div class="post__main">
                            <blockquote>
                                <p><?= esc($post['content']) ?></p>
                                <cite><?= esc($post['cite_author']) ?></cite>
                            </blockquote>
                        </div>
                    </div>

                <?php elseif ($post['type'] === 'text'): ?>
                    <div class="post-details__image-wrapper post-text">
                        <div class="post__main">
                            <p><?= esc($post['content']) ?></p>
                        </div>
                    </div>

                <?php elseif ($post['type'] === 'link'): ?>
                    <div class="post__main">
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="//<?= esc($post['content']); ?>" title="Перейти по ссылке <?= esc($post['content']); ?>">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=<?= esc($post['content']); ?>" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= esc($post['content']); ?></h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                <?php elseif ($post['type'] === 'video'): ?>
                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                        <?= embedYoutubeVideo(esc($post['content'])); ?>
                    </div>

                <?php endif; ?>

                <div class="post__indicators">
                    <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                            <svg class="post__indicator-icon" width="20" height="17">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                            <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                <use xlink:href="#icon-heart-active"></use>
                            </svg>
                            <span><?= esc($post['likes_count']); ?></span>
                            <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                            <svg class="post__indicator-icon" width="19" height="17">
                                <use xlink:href="#icon-comment"></use>
                            </svg>
                            <span><?= esc($post['comments_count']); ?></span>
                            <span class="visually-hidden">количество комментариев</span>
                        </a>
                    </div>
                    <span class="post__view"><?= esc($post['views_count']); ?> просмотров</span>
                </div>

                <?php if ($hashtags): ?>
                    <ul class="post__tags">
                        <?php foreach ($hashtags as $hashtag): ?>
                            <li>
                                <a href="/search.php?<?= esc(http_build_query(['result' => '#' . $hashtag['name']])) ?>">
                                    #<?= esc($hashtag['name']); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="comments" id="comments">
                    <form class="comments__form form" action="/post.php?<?= esc(http_build_query(['id' => $post['id']])) ?>" method="post">
                        <div class="comments__my-avatar">
                            <?php if ($userData['avatar']): ?>
                                <img class="comments__picture" src="../../uploads/avatars/<?= esc($userData['avatar']) ?>" alt="Аватар пользователя">
                            <?php endif; ?>
                        </div>
                        <div class="form__input-section<?= !empty($errors) ? ' form__input-section--error' : ''; ?>">
                            <textarea class="comments__textarea form__textarea form__input" name="comment" placeholder="Ваш комментарий"><?= !empty($errors) ? esc(getPostVal('comment')) : ''; ?></textarea>
                            <label class="visually-hidden">Ваш комментарий</label>
                            <?php if (!empty($errors)): ?>
                                <?= includeTemplate('field-error.php', [
                                    'errorTitle' => $errors['title'] ?? null,
                                    'errorDesc' => $errors['description'] ?? null,
                                ], PARTS_DIR); ?>
                            <?php endif; ?>
                        </div>
                        <button class="comments__submit button button--green" type="submit">Отправить</button>
                        <input name="post-id" type="hidden" value="<?= esc($post['id']); ?>">
                    </form>

                    <?php if ($comments): ?>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">

                                <?php foreach ($comments as $comment): ?>
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="/profile.php?<?= esc(http_build_query(['id' => $comment['user_id']])); ?>">
                                                <?php if ($comment['author_avatar']): ?>
                                                    <img class="comments__picture" src="/uploads/avatars/<?= esc($comment['author_avatar']) ?>" alt="Аватар пользователя <?= esc($comment['author']); ?>">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name" href="/profile.php?<?= esc(http_build_query(['id' => $comment['user_id']])); ?>">
                                                    <span><?= esc($comment['author']); ?></span>
                                                </a>
                                                <time class="comments__time" datetime="<?= esc($comment['date']); ?>"><?= esc(getRelativeDateFormat($comment['date'],
                                                        "назад")); ?></time>
                                            </div>
                                            <p class="comments__text"><?= esc($comment['text']); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="post-details__user user">
                <div class="post-details__user-info user__info">
                    <div class="post-details__avatar user__avatar">
                        <a class="post-details__avatar-link user__avatar-link"
                           href="/profile.php?id=<?= esc($post['author_id']); ?>">
                            <?php if ($post['avatar']): ?>
                                <img class="post-details__picture user__picture"
                                     src="uploads/avatars/<?= esc($post['avatar']); ?>"
                                     alt="Аватар пользователя <?= esc($post['author']); ?>">
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="post-details__name-wrapper user__name-wrapper">
                        <a class="post-details__name user__name" href="/profile.php?id=<?= esc($post['author_id']); ?>">
                            <span><?= esc($post['author']); ?></span>
                        </a>
                        <time class="post-details__time user__time"
                              datetime="<?= esc($post['author_reg_date']); ?>">
                            <?= esc(getRelativeDateFormat($post['author_reg_date'], "на сайте")); ?>
                        </time>
                    </div>
                </div>
                <div class="post-details__rating user__rating">
                    <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="post-details__rating-amount user__rating-amount">
                            <?= esc($post['subscriptions_count']); ?>
                        </span>
                        <span class="post-details__rating-text user__rating-text">подписчиков</span>
                    </p>
                    <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                        <span class="post-details__rating-amount user__rating-amount">
                            <?= esc($post['publications_count']); ?>
                        </span>
                        <span class="post-details__rating-text user__rating-text">публикаций</span>
                    </p>
                </div>
                <?php if ($post['author_id'] !== $_SESSION['id']): ?>
                    <div class="post-details__user-buttons user__buttons">
                        <a class="user__button user__button--subscription button button--main
                    <?= $subscribed ? ' button--quartz' : ''; ?>"
                           href="/profile.php?id=<?= esc($post['author_id']); ?>&action=<?= $subscribed ? 'unsubscribe' : 'subscribe'; ?>">
                            <?= $subscribed ? 'Отписаться' : 'Подписаться'; ?>
                        </a>
                        <?php if ($subscribed): ?>
                            <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>
</div>
