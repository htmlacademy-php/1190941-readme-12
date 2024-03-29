<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var array $postTypes
 * @var array $userData
 * @var array $postsLikedByUser
 * @var string $scriptName
 */

require 'bootstrap.php';
require 'model/posts.php';

$queryString = $_GET ?? null;

require 'modules/filter.php';
require 'modules/like.php';

$pagesCount = (int) getPagesCount($db, $queryString['type']);
$pagesCount = $pagesCount !== 0 ? $pagesCount : 1;
$limit = 6;
$totalPages = intval(ceil($pagesCount / $limit));
$queryString['page'] = $queryString['page'] ?? 1;

if (
    $queryString['page'] > $totalPages
    || $queryString['page'] <= 0
    || preg_match('/[^\d]+/', $queryString['page'])
) {
    get404StatusCode();
}

if (is_string($queryString['page'])) {
    $queryString['page'] = intval($queryString['page']);
}

$offset = ($queryString['page'] - 1) * $limit;

$pagination['prev'] = $queryString['page'] - 1;
$pagination['next'] = $queryString['page'] + 1;
$pagination['next'] = $pagination['next'] <= $totalPages ? $pagination['next'] : null;

$queryString['sort'] = $queryString['sort'] ?? null;
$queryString['direction'] = $queryString['direction'] ?? null;

$postData = getPosts($db, $offset, $queryString['type'], $queryString['sort'], $queryString['direction']);

foreach ($postData as &$post) {
    $post['liked'] = in_array($post['id'], $postsLikedByUser);
}

$sort = [
    'Популярность' => 'popularity',
    'Лайки' => 'likes',
    'Дата' => 'date',
];

$pageMainContent = includeTemplate('popular.php', [
    'postData' => $postData,
    'postTypes' => $postTypes,
    'queryString' => $queryString,
    'pagination' => $pagination,
    'sort' => $sort,
    'scriptName' => $scriptName,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - популярное',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'popular',
]);

print($pageLayout);
