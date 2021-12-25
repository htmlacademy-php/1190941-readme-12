<?php
/**
 * Использую логику в 2-х сценариях /feed.php и /popular.php
 * @var $db
 * @var array $queryString
 */

require 'model/types.php';

$queryString['type'] = isset($queryString['type']) ? (int) $queryString['type'] : null;

if ($queryString['type'] && !(bool) $queryString['type']) {
    get404StatusCode();
}

$postTypes = getPostTypes($db);

if ($queryString['type'] && !in_array($queryString['type'], array_column($postTypes, 'id'))) {
    get404StatusCode();
}
