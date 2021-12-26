<?php
/**
 * @var array $errors
 * @var string $fieldName
 */
?>

<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label" for="<?= esc($fieldName); ?>">Текст цитаты
        <span class="form__input-required">*</span>
    </label>
    <div class="form__input-section">
        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input"
                  id="<?= esc($fieldName); ?>" name="<?= esc($fieldName); ?>"
                  placeholder="Текст цитаты"><?= esc(getPostVal($fieldName)); ?></textarea>

        <?php if (!empty($errors) && isset($errors[$fieldName])): ?>
            <?= includeTemplate('field-error.php', [
                'errorTitle' => $errors[$fieldName]['title'] ?? null,
                'errorDesc' => $errors[$fieldName]['description'] ?? null,
            ], PARTS_DIR); ?>
        <?php endif; ?>
    </div>
</div>

<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="quote-author">Автор
        <span class="form__input-required">*</span>
    </label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author"
               value="<?= esc(getPostVal('quote-author')); ?>">

        <?php if (!empty($errors) && isset($errors['quote-author'])): ?>
            <?= includeTemplate('field-error.php', [
                'errorTitle' => $errors['quote-author']['title'] ?? null,
                'errorDesc' => $errors['quote-author']['description'] ?? null,
            ], PARTS_DIR); ?>
        <?php endif; ?>
    </div>
</div>
