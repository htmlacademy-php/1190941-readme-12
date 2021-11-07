<?php
/**
 * Использую логику в 2-х сценариях /post.php и /popular.php
 * @var $db
 */

require 'model/likes.php';

$likedByUser = getLikes($db, [$_SESSION['id']]);
$postsLikedByUser = array_column($likedByUser, 'post_id');

