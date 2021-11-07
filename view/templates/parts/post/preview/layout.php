<?php
/**
 * @var array $post
 */
?>

<article class="<?= $_SERVER['SCRIPT_NAME'] === '/popular.php' ? 'popular' : 'feed'; ?>__post post post-<?= esc($post['type']); ?>">
    <?= includeTemplate('header.php', [
        'post' => $post,
    ], POST_PREVIEW_DIR) ?>

    <div class="post__main">
        <!--здесь содержимое карточки-->
        <?= includeTemplate('post-preview.php', [
            'post' => $post,
        ], PARTS_DIR) ?>
    </div>

    <?= includeTemplate('footer.php', [
        'post' => $post,
    ], POST_PREVIEW_DIR) ?>
</article>
