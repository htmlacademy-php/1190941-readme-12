<?php
/**
 * Использую логику в 2-х сценариях /post.php и /profile.php
 * @var $db
 * @var int $profileId
 */

require 'model/subscriptions.php';

$subscriptions = checkSubscriptions($db, $_SESSION['id']);
$subscribed = in_array($profileId, array_column($subscriptions, 'user_id'));
