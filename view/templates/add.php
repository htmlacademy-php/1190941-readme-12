<?php
/**
 * @var array $postTypes
 * @var array $postType
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
                            <a class="adding-post__tabs-link filters__button filters__button--<?= esc($type['class_name']) ?><?= $type === $postType ? ' filters__button--active' : ''; ?> tabs__item tabs__item--active button" href="?<?= esc(http_build_query(['type' => $type['class_name']])); ?>">
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

                    <section class="adding-post__<?= esc($postType['class_name']); ?> tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Форма добавления</h2>

                        <form class="adding-post__form form" action="/add.php?<?= http_build_query(['type' => $postType['class_name']]) ?>"
                              method="post"<?= $postType['class_name'] === 'photo' ? ' enctype="multipart/form-data"' : '' ?>>
                            <div class="form__text-inputs-wrapper">

                                <div class="form__<?= esc($postType['class_name']); ?>-inputs">

                                    <?= includeTemplate('form-heading-tpl.php', [
                                        'type' => $postType,
                                        'errorTitle' => $errors[$postType['class_name'] . '-heading']['title'] ?? null,
                                        'errorDesc' => $errors[$postType['class_name'] . '-heading']['description'] ?? null,
                                    ], POST_ADD_DIR); ?>

                                    <?= includeTemplate("{$postType['class_name']}-fieldset.php", [
                                        'errors' => array_filter($errors, function ($key) use ($postType) {
                                            return $key !== $postType['class_name'] . '-heading' && $key !== $postType['class_name'] . '-tags';
                                        }, ARRAY_FILTER_USE_KEY),
                                        'fieldName' => $postType['class_name'] . '-main',
                                    ], POST_ADD_FIELDSETS_DIR); ?>

                                    <?= includeTemplate('form-tags-tpl.php', [
                                        'type' => $postType,
                                        'errorTitle' => $errors[$postType['class_name'] . '-tags']['title'] ?? null,
                                        'errorDesc' => $errors[$postType['class_name'] . '-tags']['description'] ?? null,
                                    ], POST_ADD_DIR); ?>

                                </div>

                                <?php if (count($errors)): ?>
                                    <?= includeTemplate('form-error.php', [
                                        'errors' => $errors,
                                    ], PARTS_DIR) ?>
                                <?php endif; ?>

                            </div>

                            <input type="hidden" name="post-type" value="<?= esc($postType['class_name']); ?>">

                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать
                                </button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

            </div>
        </div>
    </div>
</div>
