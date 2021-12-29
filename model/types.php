<?php

/**
 * @param mysqli $db
 * @return array
 */
function getPostTypes(mysqli $db): array
{
    return sqlGetMany($db, 'SELECT * FROM types;');
}
