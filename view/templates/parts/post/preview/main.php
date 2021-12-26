<?php
/**
 * @var array $post
 */
?>

<?php if ($post['type'] === 'quote'): ?>
    <blockquote>
        <p><?= esc($post['content']); ?></p>
        <cite><?= esc($post['cite_author']); ?></cite>
    </blockquote>
<?php elseif ($post['type'] === 'text'): ?>
    <p><?= $receivedText = cropText(esc($post['content'])); ?></p>
    <?php if ($receivedText !== $post['content']): ?>
        <a class="post-text__more-link" href="<?= '/post.php?id=' . esc($post['id']); ?>">Читать далее</a>
    <?php endif; ?>
<?php elseif ($post['type'] === 'photo'): ?>
    <div class="post-photo__image-wrapper">
        <img src="uploads/photos/<?= esc($post['content']); ?>"
             alt="Фото от пользователя <?= esc($post['author']); ?>"
            <?= $_SERVER['SCRIPT_NAME'] === '/popular.php' ? 'width="360" height="240"' : 'width="760" height="396"' ?>>
    </div>
<?php elseif ($post['type'] === 'link'): ?>
    <div class="post-link__wrapper">
        <a class="post-link__external" href="//<?= esc($post['content']); ?>" title="Перейти по ссылке <?= esc($post['content']); ?>">
            <div class="post-link__info-wrapper">
                <div class="post-link__icon-wrapper">
                    <img src="//www.google.com/s2/favicons?domain=<?= esc($post['content']); ?>" alt="Иконка <?= esc($post['content']); ?>">
                </div>
                <div class="post-link__info">
                    <h3><?= esc($post['title']); ?></h3>
                </div>
            </div>
            <span><?= esc($post['content']); ?></span>
        </a>
    </div>
<?php elseif ($post['type'] === 'video'): ?>
    <div class="post-video__block">
        <div class="post-video__preview">
            <?= embedYoutubeCover(esc($post['content'])); ?>
        </div>
        <a href="/" class="post-video__play-big button">
            <svg class="post-video__play-big-icon" width="14" height="14">
                <use xlink:href="#icon-video-play-big"></use>
            </svg>
            <span class="visually-hidden">Запустить проигрыватель</span>
        </a>
    </div>
<?php endif; ?>
