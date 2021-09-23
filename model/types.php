<?php

function getPostTypes ($db) {
    return sqlGetMany($db, 'SELECT * FROM types;');
}
