<?php
/**
 * @var array $errors
 * @var string $fieldName
 */
?>

<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
    <div class="form__input-section">
        <input class="adding-post__input form__input"
               id="photo-url"
               type="text"
               name="photo-url"
               placeholder="Введите ссылку"
               value="<?= esc(getPostVal('photo-url')); ?>">

        <?php if (!empty($errors) && isset($errors[$fieldName]) || isset($errors['photo-url'])): ?>
            <?= includeTemplate('template-parts/field-error.php', [
                'errorTitle' => $errors[$fieldName]['title'] ?? null,
                'errorDesc' => $errors[$fieldName]['description'] ?? null,
            ]); ?>
        <?php endif; ?>

    </div>
</div>

<div class="adding-post__input-file-container form__input-container form__input-container--file">
    <!-- TODO ограничить выбор форматов -->
    <input id="<?= esc($fieldName); ?>" type="file" name="<?= esc($fieldName); ?>" title=""
           value="<?= esc(getPostVal($fieldName)); ?>">
</div>
