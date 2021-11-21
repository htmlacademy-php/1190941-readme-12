<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var array $userData
 * @var string $scriptName
 * @var string $postsLikedByUser
 */

require 'bootstrap.php';

require 'model/posts.php';
require 'model/hashtags.php';

require 'modules/like.php';

$queryString = $_GET ?? null;
$searchResult = $queryString['result'] ?? null;
$queryText = trim($searchResult);
$posts = null;
$isHashtag = null;

if (mb_strlen($queryText) > 0) {
    if (preg_match('/^#(.*)/' , $queryText)) {
        $isHashtag = true;
        $queryText = preg_replace('/^#(.*)/' , '$1', $queryText);
    }

    $posts = searchPosts($db, [$queryText], $isHashtag ? 'hashtag' : '');

    var_dump($posts);
}

if ($posts) {
    foreach ($posts as &$post) {
        $post['liked'] = false;

        if (in_array($post['id'], (array)$postsLikedByUser)) {
            $post['liked'] = true;
        }

        $post['hashtags'] = getPostTags($db, $post['id']);
    }
}

$pageMainContent = includeTemplate('search.php', [
    'queryString' => $queryString,
    'scriptName' => $scriptName,
    'posts' => $posts,
    'queryText' => $queryText,
    'isHashtag' => $isHashtag,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - сообщения',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'search-results',
]);

print($pageLayout);
