<?php
/**
 * @var array $post
 * @var array $comments
 */
?>

<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= esc($post['title']); ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-<?= esc($post['type']); ?>">
                <div class="post-details__main-block post post--details">
                    <?php if ($post['type'] === 'photo'): ?>
                        <div class="post-details__image-wrapper post-photo__image-wrapper">
                            <img src="/view/img/photos/<?= esc($post['photo']); ?>" alt="Фото от пользователя" width="760" height="507">
                        </div>
                    <?php elseif ($post['type'] === 'text'): ?>
                        <div class="post__main">
                            <p><?= esc($post['text']); ?></p>
                        </div>
                    <?php elseif ($post['type'] === 'quote'): ?>
                        <div class="post__main">
                            <blockquote>
                                <p><?= esc($post['text']); ?></p>
                                <cite><?= esc($post['cite_author']); ?></cite>
                            </blockquote>
                        </div>
                    <?php elseif ($post['type'] === 'video'): ?>
                        <div class="post__main">
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_video($post['youtube_link']); ?>
                                </div>
                                <!--<div class="post-video__control">
                                    <button class="post-video__play post-video__play--paused button button--video" type="button">
                                        <span class="visually-hidden">Запустить видео</span>
                                    </button>
                                    <div class="post-video__scale-wrapper">
                                        <div class="post-video__scale">
                                            <div class="post-video__bar">
                                                <div class="post-video__toggle"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button">
                                        <span class="visually-hidden">Полноэкранный режим</span>
                                    </button>
                                </div>
                                <button class="post-video__play-big button" type="button">
                                    <svg class="post-video__play-big-icon" width="27" height="28">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </button>-->
                            </div>
                        </div>
                    <?php elseif ($post['type'] === 'link'): ?>
                        <div class="post__main">
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="//<?= esc($post['link']); ?>" title="Перейти по ссылке">
                                    <div class="post-link__icon-wrapper">
                                        <img src="/view/img/logo-vita.jpg" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= esc($post['title']); ?></h3>
                                        <p>Не ясно что тут должно быть</p>
                                        <span><?= esc($post['link']); ?></span>
                                    </div>
                                    <svg class="post-link__arrow" width="11" height="16">
                                        <use xlink:href="#icon-arrow-right-ad"></use>
                                    </svg>
                                </a>
                            </div>
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
                    <ul class="post__tags">
                        <li><a href="#">#nature</a></li>
                        <li><a href="#">#globe</a></li>
                        <li><a href="#">#photooftheday</a></li>
                        <li><a href="#">#canon</a></li>
                        <li><a href="#">#landscape</a></li>
                        <li><a href="#">#щикарныйвид</a></li>
                    </ul>
                    <div class="comments">
                        <form class="comments__form form" action="#" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" src="/view/img/users/userpic-anton.jpg" alt="Аватар пользователя" width="40" height="40">
                            </div>
                            <div class="form__input-section form__input-section--error">
                                <textarea class="comments__textarea form__textarea form__input" id="comments__textarea" placeholder="Ваш комментарий"></textarea>
                                <label class="visually-hidden" for="comments__textarea">Ваш комментарий</label>
                                <button class="form__error-button button" type="button">!</button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка валидации</h3>
                                    <p class="form__error-desc">Это поле обязательно к заполнению</p>
                                </div>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php foreach ($comments as $comment): ?>
                                <li class="comments__item user">
                                    <div class="comments__avatar">
                                        <a class="user__avatar-link" href="#">
                                            <img class="comments__picture" src="/view/img/users/<?= esc($comment['author_avatar']); ?>" alt="Аватар пользователя" width="40" height="40">
                                        </a>
                                    </div>
                                    <div class="comments__info">
                                        <div class="comments__name-wrapper">
                                            <a class="comments__user-name" href="#">
                                                <span><?= esc($comment['author']); ?></span>
                                            </a>
                                            <time class="comments__time" datetime="<?= esc($comment['date']); ?>"><?= get_relative_date_format($comment['date'], 'назад'); ?></time>
                                        </div>
                                        <p class="comments__text">
                                            <?= esc($comment['text']); ?>
                                        </p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <a class="comments__more-link" href="#">
                                <span>Показать все комментарии</span>
                                <sup class="comments__amount">45</sup>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="#">
                                <img class="post-details__picture user__picture" src="<?= esc(get_path(false, $post['avatar'])); ?>" alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="#">
                                <span><?= esc($post['author']); ?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="<?= esc($post['author_reg_date']); ?>"><?= esc(get_relative_date_format($post['author_reg_date'], "на сайте")); ?></time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount">0</span>
                            <span class="post-details__rating-text user__rating-text">подписчиков</span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= esc($post['publications_count']); ?></span>
                            <span class="post-details__rating-text user__rating-text">публикаций</span>
                        </p>
                    </div>
                    <div class="post-details__user-buttons user__buttons">
                        <button class="user__button user__button--subscription button button--main" type="button">Подписаться</button>
                        <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
