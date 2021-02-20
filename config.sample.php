<?php

$db_host = '127.0.0.1';
$db_username = 'mysql';
$db_password = 'mysql';
$db_database = 'readme_db';
$db_port = 3306;
$db_charset = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli($db_host, $db_username, $db_password, $db_database, $db_port);
$db->set_charset($db_charset);
