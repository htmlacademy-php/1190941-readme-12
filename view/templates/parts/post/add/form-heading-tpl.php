<?php
/**
 * @var array $type
 * @var string $errorTitle
 * @var string $errorDesc
 */
?>

<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="<?= esc($type['class_name']); ?>-heading">
        Заголовок
        <span class="form__input-required">*</span>
    </label>

    <div class="form__input-section">
        <input class="adding-post__input form__input"
               id="<?= esc($type['class_name']); ?>-heading"
               type="text" name="<?= esc($type['class_name']); ?>-heading"
               placeholder="Введите заголовок"
               value="<?= esc(getPostVal($type['class_name'] . '-heading')); ?>">

        <?php if (isset($errorTitle) && isset($errorDesc)): ?>
            <?= includeTemplate('field-error.php', [
                'errorTitle' => $errorTitle,
                'errorDesc' => $errorDesc,
            ], PARTS_DIR); ?>
        <?php endif; ?>

    </div>
</div>
