<?php
/**
 * @var array $errors
 * @var string $fieldName
 */
?>

<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label" for="<?= esc($fieldName); ?>">Текст поста
        <span class="form__input-required">*</span>
    </label>
    <div class="form__input-section">
        <textarea class="adding-post__textarea form__textarea form__input" id="<?= esc($fieldName); ?>"
                  name="<?= esc($fieldName); ?>"
                  placeholder="Введите текст публикации"><?= esc(getPostVal($fieldName)); ?></textarea>

        <?php if (!empty($errors) && isset($errors[$fieldName])): ?>
            <?= includeTemplate('template-parts/field-error.php', [
                'errorTitle' => $errors[$fieldName]['title'] ?? null,
                'errorDesc' => $errors[$fieldName]['description'] ?? null,
            ]); ?>
        <?php endif; ?>
    </div>
</div>
