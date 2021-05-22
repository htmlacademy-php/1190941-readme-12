<?php
/**
 * @var array $post_types
 * @var array $posts
 * @var int $total_pages
 * @var string $page_main_content
 * @var array $query_string
 * @var array $pagination
 * @var array $sort
 */
?>

<section class="page__main page__main--popular">
    <div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <?php foreach ($sort as $name => $sort_name): ?>
                    <?php $class_active = $query_string['sort'] === $sort_name ? ' sorting__link--active' : '' ?>
                    <?php $sort_direction = $class_active && $query_string['direction'] === 'desc' ? 'asc' : 'desc' ?>
                    <?php $class_reverse = $sort_direction === 'desc' ? ' sorting__link--reverse' : '' ?>
                    <?php $href = get_query_string($query_string, ['sort' => $sort_name, 'page' => null, 'direction' => $sort_direction]) ?>
                <li class="sorting__item">
                    <a class="sorting__link<?= $class_active ?><?= $class_reverse ?>" href="<?= esc($href) ?>">
                        <span><?= $name; ?></span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="popular__filters filters">
            <b class="popular__filters-caption filters__caption">Тип контента:</b>
            <ul class="popular__filters-list filters__list">
                <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                    <a class="filters__button filters__button--ellipse filters__button--all <?= ($query_string['type']) ?: 'filters__button--active' ?>" href="/">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($post_types as $type): ?>
                <li class="popular__filters-item filters__item">
                    <a class="filters__button filters__button--<?= esc($type['class_name']); ?> button<?= !$query_string['type'] || $query_string['type'] !== $type['id'] ?: ' filters__button--active' ?>" href="<?= '?type=' . esc($type['id']); ?>">
                        <span class="visually-hidden"><?= esc($type['name']); ?></span>
                        <svg class="filters__icon" width="22" height="18">
                            <use xlink:href="#icon-filter-<?= esc($type['class_name']); ?>"></use>
                        </svg>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="popular__posts">
        <?php foreach ($posts as $post): ?>
            <article class="popular__post post-<?= esc($post['type']); ?> post">
                <header class="post__header">
                    <h2>
                        <a href="<?= '/post.php?id=' . esc($post['id']); ?>"><?= esc($post['title']); ?></a>
                    </h2>
                </header>
                <div class="post__main">
                    <?php if ($post['type'] === 'quote'): ?>
                        <blockquote>
                            <p><?= esc($post['text_content']); ?></p>
                            <cite><?= esc($post['cite_author']); ?></cite>
                        </blockquote>
                    <?php elseif ($post['type'] === 'text'):  ?>
                        <p><?= $received_text = esc(crop_text($post['text_content'])); ?></p>
                        <?php if ($received_text !== esc($post['text_content'])): ?>
                            <a class="post-text__more-link" href="<?= '/post.php?id=' . esc($post['id']); ?>">Читать далее</a>
                        <?php endif; ?>
                    <?php elseif ($post['type'] === 'photo'):  ?>
                        <div class="post-photo__image-wrapper">
                            <img src="/view/img/photos/<?= esc($post['img_name']); ?>" alt="Фото от пользователя" width="360" height="240">
                        </div>
                    <?php elseif ($post['type'] === 'link'):  ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="//<?= esc($post['link']); ?>" title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=<?= esc($post['link']); ?>" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= esc($post['title']); ?></h3>
                                    </div>
                                </div>
                                <span>https://<?= esc($post['link']); ?></span>
                            </a>
                        </div>
                    <?php elseif ($post['type'] === 'video'):  ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_cover(esc($post['youtube_link'])); ?>
                            </div>
                            <a href="<?= '/post.php?id=' . esc($post['id']); ?>" class="post-video__play-big button">
                                <svg class="post-video__play-big-icon" width="14" height="14">
                                    <use xlink:href="#icon-video-play-big"></use>
                                </svg>
                                <span class="visually-hidden">Запустить проигрыватель</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" src="/view/img/users/<?= esc($post['avatar']); ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= esc($post['author']); ?></b>
                                <time class="post__time" datetime="<?= esc($post['creation_date']); ?>" title="<?= show_title_date_format($post['creation_date']); ?>"><?= get_relative_date_format($post['creation_date'], "назад"); ?></time>
                            </div>
                        </a>
                    </div>
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
                            <span class="post__view"><?= esc($post['views_count']); ?></span>
                        </div>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if ($pagination['next'] || $pagination['prev']): ?>
    <div class="popular__page-links">
        <?php if ($pagination['prev']): ?>
            <?php $prev_link = get_query_string($query_string, ['page' => $pagination['prev'] === 1 ? null : $pagination['prev']]) ?>
            <a class="popular__page-link popular__page-link--prev button button--gray" href="<?= $prev_link ?>">Предыдущая страница</a>
        <?php endif; ?>
        <?php if ($pagination['next']): ?>
            <?php $next_link = get_query_string($query_string, ['page' => $pagination['next']]) ?>
            <a class="popular__page-link popular__page-link--next button button--gray" href="<?= $next_link ?>">Следующая страница</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
</section>
