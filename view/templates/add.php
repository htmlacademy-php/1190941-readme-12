<?php
/**
 * @var array $postTypes
 * @var array $errors
 * @var string $invalidBlock
 */
?>

<div class="page__main-section">

    <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
    </div>

    <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
            <div class="adding-post__tabs filters">

                <ul class="adding-post__tabs-list filters__list tabs__list">

                    <?php foreach ($postTypes as $type): ?>
                    <li class="adding-post__tabs-item filters__item">
                        <a class="adding-post__tabs-link filters__button filters__button--<?= esc($type['class_name']) ?><?= $type['class_name'] === getPostVal('post-type') || $type['class_name'] === 'text' && !getPostVal('post-type') ? ' filters__button--active' : ''; ?> tabs__item tabs__item--active button">
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= esc($type['class_name']) ?>"></use>
                            </svg>
                            <span><?= esc($type['name']) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>

                </ul>
            </div>

            <div class="adding-post__tab-content">

                <?php foreach ($postTypes as $type): ?>

                    <section class="adding-post__<?= esc($type['class_name']); ?> tabs__content<?= $type['class_name'] === getPostVal('post-type') || $type['class_name'] === 'text' && !getPostVal('post-type') ? ' tabs__content--active' : ''; ?>">
                        <h2 class="visually-hidden">Форма добавления <!-- TODO название записи --></h2>

                        <form class="adding-post__form form" action="/add.php"
                              method="post"<?= $type['class_name'] === 'photo' ? ' enctype="multipart/form-data"' : '' ?>>
                            <div class="form__text-inputs-wrapper">

                                <div class="form__<?= esc($type['class_name']); ?>-inputs">

                                    <?= includeTemplate('template-parts/add-post/form-heading-tpl.php', [
                                        'type' => $type,
                                        'errorTitle' => $errors[$type['class_name'] . '-heading']['title'] ?? null,
                                        'errorDesc' => $errors[$type['class_name'] . '-heading']['description'] ?? null,
                                    ]); ?>

                                    <?= includeTemplate("template-parts/add-post/fieldsets/{$type['class_name']}-fieldset.php", [
                                        'errors' => array_filter($errors, function ($key) use ($type) {
                                            return $key !== $type['class_name'] . '-heading' && $key !== $type['class_name'] . '-tags';
                                        }, ARRAY_FILTER_USE_KEY),
                                        'fieldName' => $type['class_name'] . '-main',
                                    ]); ?>

                                    <?= includeTemplate('template-parts/add-post/form-tags-tpl.php', [
                                        'type' => $type,
                                        'errorTitle' => $errors[$type['class_name'] . '-tags']['title'] ?? null,
                                        'errorDesc' => $errors[$type['class_name'] . '-tags']['description'] ?? null,
                                    ]); ?>

                                </div>

                                <?php if (count($errors)): ?>
                                <!-- TODO показывается и на других табах, нужно фиксить -->
                                <?= includeTemplate('template-parts/form-error.php', [
                                    'errors' => $errors,
                                ]) ?>
                                <?php endif; ?>

                            </div>

                            <input type="hidden" name="post-type" value="<?= esc($type['class_name']); ?>">

                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
