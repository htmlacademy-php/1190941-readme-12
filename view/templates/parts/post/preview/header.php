<?php
/**
 * @var array $post
 */
?>

<!-- TODO $_SERVER не безопасно, вынести в контроллер и передавать переменную с именем скрипта в шаблон -->
<?php if ($_SERVER['SCRIPT_NAME'] === '/popular.php'): ?>
<header class="post__header">
    <h2>
        <!-- FIXME прочесть доку по srintf или использовать getQuery, может удобнее будет-->
        <a href="<?= '/post.php?id=' . esc($post['id']); ?>"><?= esc($post['title']); ?></a>
    </h2>
</header>
<?php elseif ($_SERVER['SCRIPT_NAME'] === '/feed.php'): ?>
<header class="post__header post__author">
    <a class="post__author-link" href="/profile.php?id=<?= esc($post['author_id']) ?>"
       title="Автор">
        <div class="post__avatar-wrapper">
            <img class="post__author-avatar" src="/uploads/avatars/<?= esc($post['avatar']) ?>"
                 alt="Аватар пользователя" width="60" height="60">
        </div>
        <div class="post__info">
            <b class="post__author-name"><?= esc($post['author']) ?></b>
            <span class="post__time"><?= getRelativeDateFormat(esc($post['creation_date']), 'назад'); ?></span>
        </div>
    </a>
</header>
<?php endif; ?>
