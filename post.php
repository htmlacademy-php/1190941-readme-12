<?php
/**
 * @var $db
 * @var bool $isAuth
 * @var array $userData
 * @var array $postsLikedByUser
 * @var bool $subscribed
 */

require 'bootstrap.php';

require 'model/posts.php';
require 'model/comments.php';
require 'model/hashtags.php';

require 'modules/like.php';

$queryString = $_GET ?? null;
$action = $queryString['action'] ?? null;

if (!is_string($_GET['id'])) {
    get404StatusCode();
}

$id = $queryString['id'] ?? null;

if ($action === 'like' && !in_array($id, $postsLikedByUser)) {
    insertLike($db, $id, $_SESSION['id']);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
} elseif ($action === 'dislike' && in_array($id, $postsLikedByUser)) {
    deleteLike($db, $id, $_SESSION['id']);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

if (is_string($id)) {
    $id = intval($id);
}

incrementViewsCount($db, $id);

$post = getPostById($db, $id);

if ($action === 'repost' && $post && $_SESSION['id'] !== $post['author_id']) {
    insertRepost($db, $_SESSION['id'], $id);
    header('Location: ' . '/profile.php?id=' . $_SESSION['id'] . '&show=posts');
}

$profileId = $post['author_id'] ?? null;

$formData = $_POST ?? null;
$formDataPostId = $formData['post-id'] ?? null;
$comment = $formData['comment'] ?? null;
$errors = [];

if ($formData && getPostById($db, $formDataPostId)) {
    if (empty($comment)) {
        $errors['title'] = 'Все упало';
        $errors['description'] = 'Это поле должно быть заполнено';
    } else {
        if (mb_strlen(trim($comment)) < 4) {
            $errors['title'] = 'Все упало';
            $errors['description'] = 'Комментарий должен быть длиннее 4 символов не включая пробелы';
        }
    }

    if (empty($errors)) {
        insertNewComment($db, trim($comment), $formDataPostId, $_SESSION['id']);

        header('Location: /profile.php?id=' . $profileId);
    }
}

require 'modules/subscriptions.php';

if (!$post) {
    get404StatusCode();
}

$comments = getPostComments($db, $id);
$hashtags = getPostTags($db, $id);

$pageMainContent = includeTemplate('post.php', [
    'post' => $post,
    'comments' => $comments,
    'hashtags' => $hashtags,
    'queryString' => $queryString,
    'subscribed' => $subscribed,
    'userData' => $userData,
    'errors' => $errors,
]);

$pageLayout = includeTemplate('layout.php', [
    'pageTitle' => $post['title'] . ' ▶️ Пост на Readme',
    'isAuth' => $isAuth,
    'userData' => $userData,
    'pageMainContent' => $pageMainContent,
    'pageMainClass' => 'publication',
]);

print($pageLayout);
