<?php
/**
 * @var $db
 * @var array $queryString
 * @var int $isAuth
 * @var string $userName
 */

require 'bootstrap.php';
require 'model/types.php';
require 'model/posts.php';

$queryString = $_GET ?? null;
$queryString['type'] = $queryString['type'] ?? null;

// TODO подумать как переписать условие
if (!is_string($queryString['type'])
    && $queryString['type'] !== null
    || $queryString['type'] === '0'
    || $queryString['type'] === ''
) {
    get404StatusCode();
}

$postTypes = getPostTypes($db);

if ($queryString['type'] && !in_array($queryString['type'], array_column($postTypes, 'id'))) {
    get404StatusCode();
}

$pagesCount = getPagesCount($db, $queryString['type']);
$limit = 6;
$totalPages = intval(ceil($pagesCount / $limit));
$queryString['page'] = $queryString['page'] ?? 1;

// TODO подумать как учесть ?page=1fskdfhkj
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
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'popular',
]);

print($pageLayout);
