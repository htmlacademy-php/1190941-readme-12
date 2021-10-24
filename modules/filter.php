<?php
/**
 * Использую логику в 2-х сценариях /feed.php и /popular.php
 * @var $db
 */

require 'model/types.php';

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
