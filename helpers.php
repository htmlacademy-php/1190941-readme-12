<?php

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function getNounPluralForm(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $fileName Имя файла
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function includeTemplate(string $fileName, array $data = [], string $dir = TPL_DIR): string
{
    $fullPath = $dir . $fileName;

    ob_start();
    extract($data);
    require $fullPath;

    return ob_get_clean();
}

/**
 * Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 *
 * @return string Ошибку если валидация не прошла
 */
function checkYoutubeUrl($url)
{
    $id = extractYoutubeId($url);

    set_error_handler(function () {
    }, E_WARNING);
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=https://www.youtube.com/watch?v=' . $id);
    restore_error_handler();

    if (!is_array($headers)) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    $err_flag = strpos($headers[0], '200') ? 200 : 404;

    if ($err_flag !== 200) {
        return "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    return true;
}

/**
 * Возвращает код iframe для вставки youtube видео на страницу
 * @param string $youtubeUrl Ссылка на youtube видео
 * @return string
 */
function embedYoutubeVideo($youtubeUrl)
{
    $res = "";
    $id = extractYoutubeId($youtubeUrl);

    if ($id) {
        $src = "https://www.youtube.com/embed/" . $id;
        $res = '<iframe width="760" height="400" src="' . $src . '" frameborder="0"></iframe>';
    }

    return $res;
}

/**
 * Возвращает img-тег с обложкой видео для вставки на страницу
 * @param string $youtubeUrl Ссылка на youtube видео
 * @return string
 */
function embedYoutubeCover(string $youtubeUrl): string
{
    $res = "";
    $id = extractYoutubeId($youtubeUrl);

    if ($id) {
        $src = sprintf("https://img.youtube.com/vi/%s/mqdefault.jpg", $id);
        $res = '<img alt="youtube cover" width="320" height="120" src="' . $src . '" />';

        if ($_SERVER['SCRIPT_NAME'] === '/feed.php') {
            $src = sprintf("https://img.youtube.com/vi/%s/maxresdefault.jpg", $id);
            $res = '<img alt="youtube cover" width="760" height="396" src="' . $src . '" />';
        }
    }

    return $res;
}

/**
 * Извлекает из ссылки на youtube видео его уникальный ID
 * @param string $youtubeUrl Ссылка на youtube видео
 * @return array
 */
function extractYoutubeId(string $youtubeUrl)
{
    $id = false;

    $parts = parse_url($youtubeUrl);
    $parts['host'] = $parts['host'] ?? null;

    if ($parts) {
        if ($parts['path'] === '/watch') {
            parse_str($parts['query'], $vars);
            $id = $vars['v'] ?? null;
        } else {
            if ($parts['host'] === 'youtu.be') {
                $id = substr($parts['path'], 1);
            }
        }
    }

    return $id;
}

/**
 * Обрезает текст до фиксированной длинны
 * @param string $text Редактируемая строка
 * @param int $maxChars Кол-во символов до которого нужно обрезать строку
 * @return string Строка длинной не больше $maxChars символов
 */
function cropText(string $text, int $maxChars = 300): string
{
    if (mb_strlen($text) < $maxChars) {
        return $text;
    }

    $totalChars = 0;
    $spaceValue = 1;
    $verifiedText = [];
    $textParts = explode(' ', $text);

    foreach ($textParts as $textPart) {
        $totalChars += mb_strlen($textPart) + $spaceValue;

        if (($totalChars - $spaceValue) >= $maxChars) {
            break;
        }

        $verifiedText[] = $textPart;
    }

    $text = implode(' ', $verifiedText);

    return $text . ' ...';
}

/**
 * Экранирует спец. символы
 * @param $content
 *
 * @return string
 */
function esc($content): string
{
    return htmlspecialchars($content, ENT_QUOTES);
}

/**
 * Приводит $dateTime к заданному $format
 * @param string $dateTime
 * @param string $format
 *
 * @return string
 */
function formatDate(string $dateTime, string $format): string
{
    $dateTime = new DateTime($dateTime, new DateTimeZone('Europe/Moscow'));

    return $dateTime->format($format);
}

/**
 * Приводит $postDate к заданному формату
 * @param string $postDate
 * @param string $stringEnd
 *
 * @return string
 */
function getRelativeDateFormat(string $postDate, string $stringEnd): string
{
    $postDate = new DateTime($postDate, new DateTimeZone('Europe/Moscow'));
    $currentDate = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $dateTimeDiff = $postDate->diff($currentDate);
    $correctDateFormat = '';

    if ($dateTimeDiff->y !== 0) {
        $years = $dateTimeDiff->y;
        $correctDateFormat = sprintf("{$years} %s {$stringEnd}", getNounPluralForm($years, 'год', 'года', 'лет'));
    } elseif ($dateTimeDiff->m !== 0) {
        $months = $dateTimeDiff->m;
        $correctDateFormat = sprintf("{$months} %s {$stringEnd}",
            getNounPluralForm($months, 'месяц', 'месяца', 'месяцев'));
    } elseif ($dateTimeDiff->d >= 7) {
        $weeks = floor($dateTimeDiff->d / 7);
        $correctDateFormat = sprintf("{$weeks} %s {$stringEnd}",
            getNounPluralForm($weeks, 'неделю', 'недели', 'недели'));
    } elseif ($dateTimeDiff->d < 7 && $dateTimeDiff->d !== 0) {
        $days = $dateTimeDiff->d;
        $correctDateFormat = sprintf("{$days} %s {$stringEnd}", getNounPluralForm($days, 'день', 'дня', 'дней'));
    } elseif ($dateTimeDiff->h !== 0) {
        $hours = $dateTimeDiff->h;
        $correctDateFormat = sprintf("{$hours} %s {$stringEnd}", getNounPluralForm($hours, 'час', 'часа', 'часов'));
    } elseif ($dateTimeDiff->i !== 0) {
        $minutes = $dateTimeDiff->i;
        $correctDateFormat = sprintf("{$minutes} %s {$stringEnd}",
            getNounPluralForm($minutes, 'минуту', 'минуты', 'минут'));
    }

    return $correctDateFormat;
}

/**
 * @param mysqli $db
 * @param string $sql
 * @param array $params
 */
function preparedQuery(mysqli $db, string $sql, array $params)
{
    $types = str_repeat('s', count($params));
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt;
}

/**
 * Получает результат из подготовленного запроса в виде объекта mysqli_result
 * @param mysqli $db
 * @param string $sql
 * @param array|null $params
 *
 * @return mysqli_result
 */
function sqlSelect(mysqli $db, string $sql, array $params = null): mysqli_result
{
    if (!$params) {
        return $db->query($sql);
    }

    return preparedQuery($db, $sql, $params)->get_result();
}

/**
 * Выбирает строку из набора результатов и помещает её в ассоциативный массив
 * @param mysqli $db
 * @param string $sql
 * @param array|null $params
 *
 * @return array|null|false
 */
function sqlGetSingle(mysqli $db, string $sql, array $params = null)
{
    return sqlSelect($db, $sql, $params)->fetch_assoc();
}

/**
 * Выбирает все строки из результирующего набора и помещает их в ассоциативный массив, обычный массив или в оба
 * @param mysqli $db
 * @param string $sql
 * @param array|null $params
 *
 * @return array
 */
function sqlGetMany(mysqli $db, string $sql, array $params = null): array
{
    return sqlSelect($db, $sql, $params)->fetch_all(MYSQLI_ASSOC);
}

/**
 * @param array $queryString
 * @param array $modifier
 * @return string
 */
function getQueryString(array $queryString, array $modifier): string
{
    $mergedArray = array_merge($queryString, $modifier);

    return array_filter($mergedArray) ? '?' . http_build_query($mergedArray) : '/';
}

/**
 * Отдает 404 статус код
 * @return void
 */
function get404StatusCode()
{
    http_response_code(404);
    exit();
}

/**
 * Сохраняет данные полей при ошибке
 * @param $name
 * @return mixed|string
 */
function getPostVal($name)
{
    return $_POST[$name] ?? "";
}
