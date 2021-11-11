<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var array $postTypes
 * @var array $userData
 * @var array $postsLikedByUser
 */

require 'bootstrap.php';
require 'model/posts.php';

$queryString = $_GET ?? null;

require 'modules/filter.php';
require 'modules/like.php';

$pagesCount = getPagesCount($db, $queryString['type']);
$limit = 6;
$totalPages = intval(ceil($pagesCount / $limit));
$queryString['page'] = $queryString['page'] ?? 1;

// QSTN подумать как учесть ?page=1fskdfhkj
if ($queryString['page'] > $totalPages || $queryString['page'] <= 0) {
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
    $post['liked'] = false;

    if (in_array($post['id'], $postsLikedByUser)) {
        $post['liked'] = true;
    }
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
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => 'Readme - популярное',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'popular',
]);

print($pageLayout);
