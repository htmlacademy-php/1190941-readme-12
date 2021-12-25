<?php

function getPostTypes (mysqli $db) {
    return sqlGetMany($db, 'SELECT * FROM types;');
}
