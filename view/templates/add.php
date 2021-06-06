<?php
/**
 * @var array $post_types
 */

$error_message = [
    'heading' => 'Заголовок сообщения',
    'description' => 'Текст сообщения об ошибке, подробно объясняющий, что не так.',
];

$invalid_list = [
    'Заголовок. Это поле должно быть заполнено.',
    'Цитата. Она не должна превышать 70 знаков.',
]

?>

<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php $current_type = $_POST['post-type'] ?? 'text' ?>
                        <?php foreach ($post_types as $type): ?>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--<?= esc($type['class_name']); ?> <?= $type['class_name'] === $current_type ? 'filters__button--active tabs__item--active' : ''; ?> tabs__item button">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= esc($type['class_name']); ?>"></use>
                                </svg>
                                <span><?= esc($type['name']); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content">

                    <section class="adding-post__text tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Форма добавления текста</h2>
                        <form class="adding-post__form form" action="/add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="text-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="text-heading" type="text" name="post-heading" placeholder="Введите заголовок">
                                            <?= include_template('form-components/error-message.php', $error_message) ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                                        <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="post-text" placeholder="Введите текст публикации"></textarea>
                                            <?= include_template('form-components/error-message.php', $error_message) ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="post-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="post-tags" type="text" name="post-tags" placeholder="Введите подходящие теги">
                                            <?= include_template('form-components/error-message.php', $error_message) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php require 'form-components/invalid-list.php' ?>
                            </div>
                            <div class="adding-post__buttons">
                                <input type="hidden" name="post-type" value="text">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                    <section class="adding-post__quote tabs__content">
                        <h2 class="visually-hidden">Форма добавления цитаты</h2>
                        <form class="adding-post__form form" action="/add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="quote-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="quote-heading" type="text" name="post-heading" placeholder="Введите заголовок">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                                        <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="quote-text" placeholder="Текст цитаты"></textarea>
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" placeholder="Введите имя автора">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="cite-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="cite-tags" type="text" name="post-tags" placeholder="Введите подходящие теги">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php require 'form-components/invalid-list.php' ?>
                            </div>
                            <div class="adding-post__buttons">
                                <input type="hidden" name="post-type" value="quote">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                    <section class="adding-post__photo tabs__content">
                        <h2 class="visually-hidden">Форма добавления фото</h2>
                        <form class="adding-post__form form" action="/add.php" method="post" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-heading" type="text" name="post-heading" placeholder="Введите заголовок">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-link" placeholder="Введите ссылку на изображение">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-tags" type="text" name="post-tags" placeholder="Введите подходящие теги">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php require 'form-components/invalid-list.php' ?>
                            </div>
                            <div class="adding-post__input-file-container form__input-container form__input-container--file">
                                <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                                    <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                                        <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="userpic-file-photo" title=" ">
                                        <div class="form__file-zone-text">
                                            <span>Перетащите фото сюда</span>
                                        </div>
                                    </div>
                                    <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                                        <span>Выбрать фото</span>
                                        <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                                            <use xlink:href="#icon-attach"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

                                </div>
                            </div>
                            <div class="adding-post__buttons">
                                <input type="hidden" name="post-type" value="photo">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                    <section class="adding-post__video tabs__content">
                        <h2 class="visually-hidden">Форма добавления видео</h2>
                        <form class="adding-post__form form" action="/add.php" method="post" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="video-heading" type="text" name="post-heading" placeholder="Введите заголовок">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="video-url" type="text" name="video-link" placeholder="Введите ссылку на видео">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="video-tags" type="text" name="post-tags" placeholder="Введите подходящие теги">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php require 'form-components/invalid-list.php' ?>
                            </div>

                            <div class="adding-post__buttons">
                                <input type="hidden" name="post-type" value="video">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                    <section class="adding-post__link tabs__content">
                        <h2 class="visually-hidden">Форма добавления ссылки</h2>
                        <form class="adding-post__form form" action="/add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="link-heading" type="text" name="post-heading" placeholder="Введите заголовок">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="post-link" type="text" name="post-link" placeholder="Введите ссылку">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="link-tags">Теги</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="link-tags" type="text" name="post-tags" placeholder="Введите подходящие теги">
                                            <?php require 'form-components/error-message.php' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php require 'form-components/invalid-list.php' ?>
                            </div>
                            <div class="adding-post__buttons">
                                <input type="hidden" name="post-type" value="link">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
</main>
