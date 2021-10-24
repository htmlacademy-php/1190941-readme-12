<?php
/**
 * Использую логику в 2-х сценариях /post.php и /profile.php
 * @var $db
 * @var int $profileId
 */

require 'model/subscriptions.php';

$subscriptions = checkSubscription($db, [$_SESSION['id'], $profileId]);

$subscribed = null;

foreach ($subscriptions as $subscription) {
    $subscribed = $subscription['user_id'] === $profileId;
    break;
}
