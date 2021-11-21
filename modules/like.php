<?php
/**
 * Использую логику в 3-х сценариях /post.php, /popular.php, /search.php
 * @var $db
 */

require 'model/likes.php';

$likedByUser = getLikes($db, [$_SESSION['id']]);
$postsLikedByUser = array_column($likedByUser, 'post_id');

