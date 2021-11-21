<?php
/**
 * @var string $scriptName
 * @var array $posts
 * @var string $queryText
 */
?>

<h1 class="visually-hidden">Страница результатов поиска</h1>

<section class="search">
    <h2 class="visually-hidden">Результаты поиска</h2>

    <div class="search__query-wrapper">
        <div class="search__query container">
            <span>Вы искали:</span>
            <span class="search__query-text"><?= esc($queryText); ?></span>
        </div>
    </div>

    <div class="search__results-wrapper">
        <div class="container">
            <div class="search__content">

                <?php foreach ($posts as $post): ?>
                    <?= includeTemplate('layout.php', [
                        'post' => $post,
                        'scriptName' => $scriptName,
                    ], POST_PREVIEW_DIR) ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>
