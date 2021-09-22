<?php
/**
 * @var array $type
 * @var string $errorTitle
 * @var string $errorDesc
 */
?>

<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="<?= esc($type['class_name']); ?>-tags">Теги</label>
    <div class="form__input-section">
        <input class="adding-post__input form__input"
               id="<?= esc($type['class_name']); ?>-tags"
               type="text"
               name="<?= esc($type['class_name']); ?>-tags"
               placeholder="Введите теги"
               value="<?= esc(getPostVal($type['class_name'] . '-tags')); ?>">

        <?php if (isset($errorTitle) && isset($errorDesc)): ?>
            <?= includeTemplate('template-parts/field-error.php', [
                'errorTitle' => $errorTitle,
                'errorDesc' => $errorDesc,
            ]); ?>
        <?php endif; ?>
    </div>
</div>
