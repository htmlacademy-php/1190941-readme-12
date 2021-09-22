<?php
/**
 * @var array $errors;
 */

var_dump($errors);
?>

<div class="container">
    <h1 class="page__title page__title--registration">Регистрация</h1>
</div>

<section class="registration container">
    <h2 class="visually-hidden">Форма регистрации</h2>

    <form class="registration__form form" action="/registration.php" method="post" enctype="multipart/form-data">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="registration__input-wrapper form__input-wrapper">

                    <label class="registration__label form__label" for="registration-email">Электронная почта
                        <span class="form__input-required">*</span>
                    </label>

                    <div class="form__input-section">

                        <input class="registration__input form__input"
                               id="registration-email"
                               type="email"
                               name="email"
                               placeholder="Укажите эл.почту"
                               value="<?= esc(getPostVal('email')); ?>">

                        <?php if (isset($errors['email'])): ?>
                            <?= includeTemplate('template-parts/field-error.php', [
                                'errorTitle' => $errors['email']['title'],
                                'errorDesc' => $errors['email']['description'],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="registration__input-wrapper form__input-wrapper">

                    <label class="registration__label form__label" for="registration-login">Логин
                        <span class="form__input-required">*</span>
                    </label>

                    <div class="form__input-section">

                        <input class="registration__input form__input"
                               id="registration-login"
                               type="text"
                               name="login"
                               placeholder="Укажите логин"
                               value="<?= esc(getPostVal('login')); ?>">

                        <?php if (isset($errors['login'])): ?>
                            <?= includeTemplate('/template-parts/field-error.php', [
                                'errorTitle' => $errors['login']['title'],
                                'errorDesc' => $errors['login']['description'],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label" for="registration-password">Пароль
                        <span class="form__input-required">*</span>
                    </label>
                    <div class="form__input-section">

                        <input class="registration__input form__input"
                               id="registration-password"
                               type="password"
                               name="password"
                               placeholder="Придумайте пароль">

                        <?php if (isset($errors['password'])): ?>
                            <?= includeTemplate('/template-parts/field-error.php', [
                                'errorTitle' => $errors['password']['title'],
                                'errorDesc' => $errors['password']['description'],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="registration__input-wrapper form__input-wrapper">
                    <label class="registration__label form__label" for="registration-password-repeat">Повтор пароля
                        <span class="form__input-required">*</span>
                    </label>
                    <div class="form__input-section">

                        <input class="registration__input form__input"
                               id="registration-password-repeat"
                               type="password"
                               name="password-repeat"
                               placeholder="Повторите пароль">

                        <?php if (isset($errors['password-repeat'])): ?>
                            <?= includeTemplate('/template-parts/field-error.php', [
                                'errorTitle' => $errors['password-repeat']['title'],
                                'errorDesc' => $errors['password-repeat']['description'],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (count($errors)): ?>
            <?= includeTemplate('template-parts/form-error.php', [
                'errors' => $errors,
            ]) ?>
            <?php endif; ?>
        </div>
        <div class="registration__input-file-container form__input-container form__input-container--file">
            <div class="registration__input-file-wrapper form__input-file-wrapper">

                <input id="userpic-file"
                       type="file"
                       name="avatar-file"
                       title=""
                       value="<?= esc(getPostVal('userpic-file')); ?>">

                <?php if (isset($errors['avatar-file'])): ?>
                    <?= includeTemplate('/template-parts/field-error.php', [
                        'errorTitle' => $errors['avatar']['title'],
                        'errorDesc' => $errors['avatar']['description'],
                    ]); ?>
                <?php endif; ?>

            </div>
        </div>

        <button class="registration__submit button button--main" type="submit">Отправить</button>
    </form>
</section>
