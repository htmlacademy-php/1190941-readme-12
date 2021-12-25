<?php
/**
 * @var array $profileData
 * @var array $profileTabs
 * @var array $activeTab
 * @var array $queryString
 * @var int $profileId
 * @var bool $subscribed
 * @var array $userPosts
 * @var string $scriptName
 * @var array $userPostsLikedByUsers
 * @var array $subscribedUsers
 */
?>

<h1 class="visually-hidden">Профиль</h1>

<div class="profile profile--default">
    <div class="profile__user-wrapper">
        <div class="profile__user user container">
            <div class="profile__user-info user__info" style="align-items: center">
                <div class="profile__avatar user__avatar">
                    <?php if ($profileData['avatar']): ?>
                    <img class="profile__picture user__picture"
                         src="/uploads/avatars/<?= esc($profileData['avatar']) ?>"
                         alt="Аватар пользователя" width="100" height="100">
                    <?php endif; ?>
                </div>
                <div class="profile__name-wrapper user__name-wrapper">
                    <span class="profile__name user__name"><?= esc($profileData['name']) ?></span>
                    <time class="profile__user-time user__time" datetime="<?= esc($profileData['date']); ?>">
                        <?= esc(getRelativeDateFormat($profileData['date'], "на сайте")); ?>
                    </time>
                </div>
            </div>
            <div class="profile__rating user__rating">
                <p class="profile__rating-item user__rating-item user__rating-item--publications">
                    <span class="user__rating-amount"><?= esc($profileData['publications_count']) ?></span>
                    <span class="profile__rating-text user__rating-text">публикаций</span>
                </p>
                <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                    <span class="user__rating-amount"><?= esc($profileData['subscriptions_count']) ?></span>
                    <span class="profile__rating-text user__rating-text">подписчиков</span>
                </p>
            </div>

            <?php if ($_SESSION['id'] !== $profileId): ?>
            <div class="profile__user-buttons user__buttons">
                <a class="profile__user-button user__button user__button--subscription button button--main
                <?= $subscribed ? ' button--quartz' : ''; ?>"
                   href="/profile.php?id=<?= esc($profileId); ?>&action=<?= $subscribed ? 'unsubscribe' : 'subscribe'; ?>">
                    <?= $subscribed ? 'Отписаться' : 'Подписаться'; ?>
                </a>
                <?php if ($subscribed): ?>
                <a class="profile__user-button user__button user__button--writing button button--green"
                   href="/messages.php?<?= esc(http_build_query(['chat' => $profileId])) ?>">Сообщение</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="profile__tabs-wrapper tabs">
        <div class="container">
            <div class="profile__tabs filters">
                <b class="profile__tabs-caption filters__caption">Показать:</b>
                <ul class="profile__tabs-list filters__list tabs__list">
                    <?php foreach ($profileTabs as $tab): ?>
                    <li class="profile__tabs-item filters__item">
                        <a class="profile__tabs-link filters__button<?= current(array_values($tab['href'])) === $activeTab ? ' filters__button--active tabs__item tabs__item--active' : ''; ?> button" href="<?= esc(getQueryString($queryString, $tab['href'])); ?>"><?= esc($tab['title']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="profile__tab-content">
                <?php if ($activeTab === 'posts'): ?>
                <section class="profile__posts tabs__content tabs__content--active">
                    <h2 class="visually-hidden">Публикации</h2>

                    <?php foreach ($userPosts as $post): ?>
                        <?= includeTemplate('layout.php', [
                            'post' => $post,
                            'scriptName' => $scriptName,
                        ], POST_PREVIEW_DIR) ?>
                    <?php endforeach; ?>
                </section>

                <?php elseif ($activeTab === 'likes'): ?>
                <section class="profile__likes tabs__content tabs__content--active">
                    <h2 class="visually-hidden">Лайки</h2>

                    <ul class="profile__likes-list">
                        <?php foreach ($userPostsLikedByUsers as $post): ?>
                            <li class="post-mini post-mini--<?= esc($post['type']); ?> post user">
                                <div class="post-mini__user-info user__info">
                                    <div class="post-mini__avatar user__avatar">
                                        <a class="user__avatar-link" href="/profile.php?<?= esc(http_build_query(['id' => $post['user_id']])); ?>">
                                            <?php if ($post['avatar']): ?>
                                            <img class="post-mini__picture user__picture" src="../../uploads/avatars/<?= esc($post['avatar']); ?>" alt="Аватар пользователя">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="post-mini__name-wrapper user__name-wrapper">
                                        <a class="post-mini__name user__name" href="/profile.php?<?= esc(http_build_query(['id' => $post['user_id']])); ?>">
                                            <span><?= esc($post['user_name']); ?></span>
                                        </a>
                                        <div class="post-mini__action">
                                            <span class="post-mini__activity user__additional">Лайкнул публикацию</span>
                                            <time class="post-mini__time user__additional" datetime="<?= esc($post['date']); ?>">
                                                <?= esc(getRelativeDateFormat($post['date'], 'назад')); ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-mini__preview">
                                    <a class="post-mini__link" href="/post.php?<?= esc(http_build_query(['id' => $post['id']])); ?>" title="Перейти на публикацию">
                                        <?php if ($post['type'] === 'photo'): ?>
                                            <div class="post-mini__image-wrapper">
                                                <img class="post-mini__image" src="../../uploads/photos/<?= esc($post['content']); ?>" width="109" height="109" alt="Превью публикации">
                                            </div>
                                            <span class="visually-hidden">Фото</span>
                                        <?php elseif ($post['type'] === 'text'): ?>
                                            <span class="visually-hidden">Текст</span>
                                            <svg class="post-mini__preview-icon" width="20" height="21">
                                                <use xlink:href="#icon-filter-text"></use>
                                            </svg>
                                        <?php elseif ($post['type'] === 'video'): ?>
                                            <div class="post-mini__image-wrapper">
                                                <img class="post-mini__image" src="../../uploads/photos/coast-small.png" width="109" height="109" alt="Превью публикации">
                                                <span class="post-mini__play-big">
                                                <svg class="post-mini__play-big-icon" width="12" height="13">
                                                  <use xlink:href="#icon-video-play-big"></use>
                                                </svg>
                                            </span>
                                            </div>
                                            <span class="visually-hidden">Видео</span>
                                        <?php elseif ($post['type'] === 'quote'): ?>
                                            <span class="visually-hidden">Цитата</span>
                                            <svg class="post-mini__preview-icon" width="21" height="20">
                                                <use xlink:href="#icon-filter-quote"></use>
                                            </svg>
                                        <?php elseif ($post['type'] === 'link'): ?>
                                            <span class="visually-hidden">Ссылка</span>
                                            <svg class="post-mini__preview-icon" width="21" height="18">
                                                <use xlink:href="#icon-filter-link"></use>
                                            </svg>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <?php elseif ($activeTab === 'subscriptions'): ?>
                <section class="profile__subscriptions tabs__content tabs__content--active">
                    <h2 class="visually-hidden">Подписки</h2>
                    <ul class="profile__subscriptions-list">
                        <?php foreach ($subscribedUsers as $user): ?>
                        <li class="post-mini post user">
                            <div class="post-mini__user-info user__info">
                                <div class="post-mini__avatar user__avatar">
                                    <a class="user__avatar-link" href="/profile.php?<?= esc(http_build_query(['id' => $user['id']])); ?>">
                                        <?php if ($user['avatar']): ?>
                                            <img class="post-mini__picture user__picture" src="../../uploads/avatars/<?= esc($user['avatar']); ?>" alt="Аватар пользователя">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="post-mini__name-wrapper user__name-wrapper">
                                    <a class="post-mini__name user__name" href="/profile.php?<?= esc(http_build_query(['id' => $user['id']])); ?>">
                                        <span><?= esc($user['name']); ?></span>
                                    </a>
                                    <time class="post-mini__time user__additional" datetime="<?= esc($user['date']); ?>"><?= esc(getRelativeDateFormat($user['date'], 'на сайте')); ?></time>
                                </div>
                            </div>
                            <div class="post-mini__rating user__rating">
                                <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                                    <span class="post-mini__rating-amount user__rating-amount"><?= esc($user['publications_count']); ?></span>
                                    <span class="post-mini__rating-text user__rating-text">публикаций</span>
                                </p>
                                <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                                    <span class="post-mini__rating-amount user__rating-amount"><?= esc($user['subscriptions_count']); ?></span>
                                    <span class="post-mini__rating-text user__rating-text">подписчиков</span>
                                </p>
                            </div>
                            <?php if ($user['id'] !== $_SESSION['id']): ?>
                            <div class="post-mini__user-buttons user__buttons">
                                <a class="post-mini__user-button user__button user__button--subscription button button--main <?= $user['curr_subscribed'] ? ' button--quartz' : ''; ?>"
                                   href="/profile.php?<?= esc(http_build_query(['id' => $user['id'], 'action' => $user['curr_subscribed'] ? 'unsubscribe' : 'subscribe'])) ?>">
                                    <?= $user['curr_subscribed'] ? 'Отписаться' : 'Подписаться'; ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
