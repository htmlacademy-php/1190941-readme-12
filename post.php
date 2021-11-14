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

// QSTN думать с 0, ?id[]=343 не передавать в getPostById(), и что-то с undefined index (вспомнить где)
if (!is_string($_GET['id'])) {
    get404StatusCode();
}

$id = $_GET['id'] ?? null;

// FIXME подумать над местом для этого кода, может вынести в отдельный модуль
// todo Для того, чтобы удалить лайк, не обязательно проверять его наличие. БД просто вернёт 0 строк в ответ
// todo Перенаправление можно вынести за пределы условия. И там и там это один и тот же код
if ($action === 'like' && !in_array($id, $postsLikedByUser)) {
    insertLike($db, [$id, $_SESSION['id']]);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
} elseif ($action === 'dislike' && in_array($id, $postsLikedByUser)) {
    deleteLike($db, [$id, $_SESSION['id']]);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

if (is_string($id)) {
    $id = intval($id);
}



incrementViewsCount($db, [$id]);

$post = getPostById($db, $id);
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
        $data['comment'] = trim($comment);
        $data['post_id'] = $formDataPostId;
        $data['author_id'] = $_SESSION['id'];

        insertNewComment($db, array_values($data));

        header('Location: /profile.php?id=' . $profileId);
    }
}

require 'modules/subscriptions.php';

// FIXME может стоит поднять выше
if (!$post) {
    get404StatusCode();
}

// fixme собрать в дату и прокидывать в шаблон одним массивом, комменты +хештеги
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
