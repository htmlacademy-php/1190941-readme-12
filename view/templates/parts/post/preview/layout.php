<?php
/**
 * @var array $post
 * @var array $scriptName
 */
?>

<article class="<?= esc($scriptName); ?>__post post post-<?= esc($post['type']); ?>">
    <?= includeTemplate('header.php', [
        'post' => $post,
        'scriptName' => $scriptName,
    ], POST_PREVIEW_DIR) ?>

    <div class="post__main">
        <?= includeTemplate('main.php', [
            'post' => $post,
        ], POST_PREVIEW_DIR) ?>
    </div>

    <?= includeTemplate('footer.php', [
        'post' => $post,
        'scriptName' => $scriptName,
    ], POST_PREVIEW_DIR) ?>
</article>
