<?php
/**
 * @var $invalid_list
 */
?>

<div class="form__invalid-block">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php foreach ($invalid_list as $invalid_item): ?>
        <li class="form__invalid-item"><?= $invalid_item; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
