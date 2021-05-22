<?php

function get_post_types ($db) {
    return sql_get_many($db, 'SELECT * FROM types;');
}
