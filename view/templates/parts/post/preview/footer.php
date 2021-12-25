<?php
/**
 * @var array $post
 * @var string $scriptName
 */
?>

<footer class="post__footer">

    <?php if ($scriptName === 'popular'): ?>
    <div class="post__author">
        <a class="post__author-link" href="/profile.php?id=<?= esc($post['author_id']); ?>" title="Автор">
            <div class="post__avatar-wrapper">
                <!--укажите путь к файлу аватара-->
                <img class="post__author-avatar" src="uploads/avatars/<?= esc($post['avatar']); ?>" alt="Аватар пользователя <?= esc($post['author']); ?>">
            </div>
            <div class="post__info">
                <b class="post__author-name"><?= esc($post['author']); ?></b>
                <time class="post__time" datetime="<?= esc($post['creation_date']); ?>" title="<?= showTitleDateFormat(esc($post['creation_date'])); ?>"><?= getRelativeDateFormat(esc($post['creation_date']), 'назад'); ?></time>
            </div>
        </a>
    </div>
    <?php endif; ?>

    <div class="post__indicators">
        <div class="post__buttons">
            <!-- FIXME переписать ссылку через getQueryString -->
            <a class="post__indicator post__indicator--likes<?= $post['liked'] ? ' post__indicator--likes-active' : ''; ?> button"
               href="/post.php?id=<?= esc($post['id']); ?>&action=<?= $post['liked'] ? 'dislike' : 'like'; ?>"
               title="<?= $post['liked'] ? 'Удалить лайк' : 'Лайк'; ?>">
                <svg class="post__indicator-icon" width="20" height="17">
                    <use xlink:href="#icon-heart"></use>
                </svg>
                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                    <use xlink:href="#icon-heart-active"></use>
                </svg>
                <span><?= esc($post['likes_count']); ?></span>
                <span class="visually-hidden">количество лайков</span>
            </a>
            <a class="post__indicator post__indicator--comments button" href="<?= '/post.php?id=' . esc($post['id']) . '#comments'; ?>" title="Комментарии">
                <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-comment"></use>
                </svg>
                <span><?= esc($post['comments_count']); ?></span>
                <span class="visually-hidden">количество комментариев</span>
            </a>
            <?php if ($scriptName === 'feed' || $scriptName === 'profile' || $scriptName === 'search'): ?>
            <a class="post__indicator post__indicator--repost button" href="/post.php?<?= esc(http_build_query(['id' => $post['id'], 'action' => 'repost'])) ?>" title="Репост">
                <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-repost"></use>
                </svg>
                <span><?= esc($post['reposts_count']) ?></span>
                <span class="visually-hidden">количество репостов</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($scriptName === 'feed' && $post['hashtags'] || $scriptName === 'search' && $post['hashtags']): ?>
        <ul class="post__tags">
            <?php foreach ($post['hashtags'] as $hashtag): ?>
                <li>
                    <a href="/search.php?<?= esc(http_build_query(['result' => '#' . $hashtag['name']])); ?>">#<?= esc($hashtag['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</footer>
