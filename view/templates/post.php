<?php
/**
 * @var array $post
 * @var array $comments
 * @var array $hashtags
 * @var array $queryString
 * @var bool $subscribed
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
                        <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                            <svg class="post__indicator-icon" width="19" height="17">
                                <use xlink:href="#icon-repost"></use>
                            </svg>
                            <span>0</span>
                            <span class="visually-hidden">количество репостов</span>
                        </a>
                    </div>
                    <span class="post__view"><?= esc($post['views_count']); ?> просмотров</span>
                </div>

                <?php if ($hashtags): ?>
                <ul class="post__tags">
                    <?php foreach ($hashtags as $hashtag): ?>
                    <li>
                        <a href="#">#<?= esc($hashtag['name']); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <div class="comments" id="comments">
                    <form class="comments__form form" action="#" method="post">
                        <div class="comments__my-avatar">
                            <img class="comments__picture" src="../../uploads/avatars/userpic-medium.jpg" alt="Аватар пользователя">
                        </div>
                        <div class="form__input-section"> <!-- Error class -> form__input-section--error -->
                            <textarea class="comments__textarea form__textarea form__input" placeholder="Ваш комментарий"></textarea>
                            <label class="visually-hidden">Ваш комментарий</label>
                            <!-- Error button <button class="form__error-button button" type="button">!</button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка валидации</h3>
                                <p class="form__error-desc">Это поле обязательно к заполнению</p>
                            </div>-->
                        </div>
                        <button class="comments__submit button button--green" type="submit">Отправить</button>
                    </form>

                    <?php if ($comments): ?>
                    <div class="comments__list-wrapper">
                        <ul class="comments__list">

                            <?php for ($i = 0; $i < count($comments) && $i < 3; $i++): ?>
                            <li class="comments__item user">
                                <div class="comments__avatar">
                                    <a class="user__avatar-link" href="#">
                                        <img class="comments__picture" src="/uploads/avatars/<?= esc($comments[$i]['author_avatar']) ?>" alt="Аватар пользователя <?= esc($comments[$i]['author']); ?>">
                                    </a>
                                </div>
                                <div class="comments__info">
                                    <div class="comments__name-wrapper">
                                        <a class="comments__user-name" href="#">
                                            <span><?= esc($comments[$i]['author']); ?></span>
                                        </a>
                                        <time class="comments__time" datetime="<?= esc($comments[$i]['date']); ?>"><?= esc(getRelativeDateFormat($comments[$i]['date'], "назад")); ?></time>
                                    </div>
                                    <p class="comments__text"><?= esc($comments[$i]['text']); ?></p>
                                </div>
                            </li>
                            <?php endfor; ?>
                        </ul>

                        <?php if ($post['comments_count'] > 3): ?>
                        <a class="comments__more-link" href="#">
                            <span>Показать все комментарии</span>
                            <sup class="comments__amount"><?= esc($post['comments_count'] - 3); ?></sup>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="post-details__user user">
                <div class="post-details__user-info user__info">
                    <div class="post-details__avatar user__avatar">
                        <a class="post-details__avatar-link user__avatar-link"
                           href="/profile.php?id=<?= esc($post['author_id']); ?>">
                            <img class="post-details__picture user__picture"
                                 src="uploads/avatars/<?= esc($post['avatar']); ?>"
                                 alt="Аватар пользователя <?= esc($post['author']); ?>">
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
                <!-- TODO не должен видеть блок когда смотрю свою запись -->
                <div class="post-details__user-buttons user__buttons">
                    <!-- FIXME Формирование ссылки выглядит странно, возможно нужно переписать через getQueryString -->
                    <a class="user__button user__button--subscription button button--main
                    <?= $subscribed ? ' button--quartz' : ''; ?>"
                       href="/profile.php?id=<?= esc($post['author_id']); ?>&action=<?= $subscribed ? 'unsubscribe' : 'subscribe'; ?>">
                        <?= $subscribed ? 'Отписаться' : 'Подписаться'; ?>
                    </a>
                    <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                </div>
            </div>
        </div>

    </section>
</div>
