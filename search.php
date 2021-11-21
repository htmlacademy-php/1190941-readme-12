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
$posts = getPostsForFeed($db, $_SESSION['id']);

foreach ($posts as &$post) {
    $post['liked'] = false;

    if (in_array($post['id'], (array)$postsLikedByUser)) {
        $post['liked'] = true;
    }

    $post['hashtags'] = getPostTags($db, $post['id']);
}

//if (mb_strlen($queryText) > 0) {
//    // todo sql запрос на поиск
//}

$pageMainContent = includeTemplate('search.php', [
    'queryString' => $queryString,
    'scriptName' => $scriptName,
    'posts' => $posts,
    'queryText' => $queryText,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - сообщения',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'search-results',
]);

print($pageLayout);
