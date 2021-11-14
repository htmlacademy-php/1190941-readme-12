<?php
/**
 * @var array $postData
 * @var array $postTypes
 * @var array $queryString
 * @var array $pagination
 * @var array $sort
 * @var string $scriptName
*/
?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>

<div class="popular container">

    <div class="popular__filters-wrapper">

        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <?php foreach ($sort as $name => $sortName): ?>
                    <?php $classActive = $queryString['sort'] === $sortName ? ' sorting__link--active' : '' ?>
                    <?php $sortDirection = $classActive && $queryString['direction'] === 'desc' ? 'asc' : 'desc' ?>
                    <?php $class_reverse = $sortDirection === 'desc' ? ' sorting__link--reverse' : '' ?>
                    <?php $href = getQueryString($queryString, ['sort' => $sortName, 'page' => null, 'direction' => $sortDirection]) ?>
                    <li class="sorting__item">
                        <a class="sorting__link<?= $classActive ?><?= $class_reverse ?>" href="<?= esc($href) ?>">
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
                    <a class="filters__button filters__button--ellipse filters__button--all
                    <?= (!$queryString['type']) ? ' filters__button--active' : '' ?>" href="/popular.php">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($postTypes as $type): ?>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--<?= esc($type['class_name']); ?> button
                        <?= !$queryString['type'] || $queryString['type'] !== $type['id']
                            ?: ' filters__button--active' ?>" href="<?= '?type=' . esc($type['id']); ?>">
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

        <?php foreach ($postData as $post): ?>
            <?= includeTemplate('layout.php', [
                'post' => $post,
                'scriptName' => $scriptName,
            ], POST_PREVIEW_DIR) ?>
        <?php endforeach; ?>

    </div>

    <?php if ($pagination['next'] || $pagination['prev']): ?>
        <div class="popular__page-links">
            <?php if ($pagination['prev']): ?>
                <?php $prev_link = getQueryString($queryString, ['page' => $pagination['prev'] === 1 ? null : $pagination['prev']]) ?>
                <a class="popular__page-link popular__page-link--prev button button--gray" href="<?= $prev_link ?>">Предыдущая страница</a>
            <?php endif; ?>
            <?php if ($pagination['next']): ?>
                <?php $next_link = getQueryString($queryString, ['page' => $pagination['next']]) ?>
                <a class="popular__page-link popular__page-link--next button button--gray" href="<?= $next_link ?>">Следующая страница</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
