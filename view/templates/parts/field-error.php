<?php
/**
 * @var string $errorTitle
 * @var string $errorDesc
*/
?>

<button class="form__error-button button" style="display: block" type="button">!
    <span class="visually-hidden">Информация об ошибке</span>
</button>

<div class="form__error-text">
    <h3 class="form__error-title"><?= esc($errorTitle); ?></h3>
    <p class="form__error-desc"><?= esc($errorDesc); ?></p>
</div>
