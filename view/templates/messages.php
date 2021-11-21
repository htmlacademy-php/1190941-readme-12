<?php
/**
 * @var array $openChats
 * @var array $chatID
 * @var array $chat
 * @var array $userData
 * @var array $errors
 */
?>

<h1 class="visually-hidden">Личные сообщения</h1>

<section class="messages tabs">
    <h2 class="visually-hidden">Сообщения</h2>

    <div class="messages__contacts">
        <ul class="messages__contacts-list tabs__list">
            <?php foreach ($openChats as $openChat): ?>
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab<?= $openChat['id'] === $chatID ? ' messages__contacts-tab--active tabs__item tabs__item--active' : '' ?>"
                       href="/messages.php?<?= esc(http_build_query(['chat' => $openChat['id']])); ?>">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar"
                                 src="../../uploads/avatars/<?= esc($openChat['avatar']) ?>"
                                 alt="Аватар пользователя">
                        </div>
                        <div class="messages__info">
                              <span class="messages__contact-name">
                                <?= esc($openChat['name']); ?>
                              </span>
                            <div class="messages__preview">
                                <?php if ($openChat['message']): ?>
                                <p class="messages__preview-text">
                                    <?= esc($openChat['message']); ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($openChat['date']): ?>
                                <time class="messages__preview-time" datetime="<?= esc($openChat['date']); ?>">
                                    <?= esc(formatDate($openChat['date'], 'H:i')); ?>
                                </time>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="messages__chat">
        <div class="messages__chat-wrapper">

            <ul class="messages__list tabs__content tabs__content--active">
                <?php foreach ($chat as $message): ?>
                <li class="messages__item<?= $message['id'] === $_SESSION['id'] ? ' messages__item--my' : ''; ?>">
                    <div class="messages__info-wrapper">
                        <div class="messages__item-avatar">
                            <a class="messages__author-link"
                               href="/profile.php?<?= esc(http_build_query(['id' => $message['id']])) ?>">
                                <?php if ($message['avatar']): ?>
                                <img class="messages__avatar"
                                     src="../../uploads/avatars/<?= esc($message['avatar']) ?>" alt="Аватар пользователя">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="messages__item-info">
                            <a class="messages__author"
                               href="/profile.php?<?= esc(http_build_query(['id' => $message['id']])) ?>">
                                <?= esc($message['name']) ?>
                            </a>
                            <time class="messages__time" datetime="<?= esc($message['date']) ?>">
                                <?= esc(getRelativeDateFormat($message['date'], 'назад')) ?>
                            </time>
                        </div>
                    </div>
                    <p class="messages__text">
                        <?= esc($message['message']); ?>
                    </p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="comments">
            <form class="comments__form form" action="/messages.php?<?= esc(http_build_query(['chat' => $chatID])) ?>" method="post">
                <div class="comments__my-avatar">
                    <?php if ($userData['avatar']): ?>
                        <img class="comments__picture"
                             src="../../uploads/avatars/<?= esc($userData['avatar']); ?>"
                             alt="Аватар пользователя">
                    <?php endif; ?>
                </div>
                <div class="form__input-section<?= !empty($errors) ? ' form__input-section--error' : ''; ?>">
                <textarea class="comments__textarea form__textarea form__input"
                          placeholder="Ваше сообщение" name="message"></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <?php if (!empty($errors)): ?>
                        <?= includeTemplate('field-error.php', [
                            'errorTitle' => $errors['title'] ?? null,
                            'errorDesc' => $errors['description'] ?? null,
                        ], PARTS_DIR); ?>
                    <?php endif; ?>
                </div>
                <input name="recipientID" type="hidden" value="<?= esc($chatID); ?>">
                <button class="comments__submit button button--green" type="submit">Отправить</button>
            </form>
        </div>
    </div>
</section>
