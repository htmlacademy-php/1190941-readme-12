<?php
/**
 * Использую логику в 2-х сценариях /post.php и /profile.php
 * @var $db
 * @var int $profileId
 */

require 'model/subscriptions.php';

// TODO проверить существует ли пользователь на которого подписываюсь

// TODO тут не верная логика, подписка в данном случае возможна всего одна
$subscriptions = checkSubscription($db, [$_SESSION['id'], $profileId]);

$subscribed = null;

// TODO эту логику тоже нужно переписать, не ясно зачем это сдесь
foreach ($subscriptions as $subscription) {
    $subscribed = $subscription['user_id'] === $profileId;
    break;
}
