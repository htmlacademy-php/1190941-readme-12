<?php
/**
 * @var array $errors;
 */
?>

<div class="form__invalid-block">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php foreach($errors as $error): ?>
            <li class="form__invalid-item"><?= esc($error['name']) ?>. <?= esc($error['description']) ?>.</li>
        <?php endforeach; ?>
    </ul>
</div>
