<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

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
        if ($parts['path'] == '/watch') {
            parse_str($parts['query'], $vars);
            $id = $vars['v'] ?? null;
        } else {
            if ($parts['host'] == 'youtu.be') {
                $id = substr($parts['path'], 1);
            }
        }
    }

    return $id;
}

/**
 * @param $index
 * @return false|string
 */
function generateRandomDate($index)
{
    $deltas = [['minutes' => 59], ['hours' => 23], ['days' => 6], ['weeks' => 4], ['months' => 11]];
    $dcnt = count($deltas);

    if ($index < 0) {
        $index = 0;
    }

    if ($index >= $dcnt) {
        $index = $dcnt - 1;
    }

    $delta = $deltas[$index];
    $timeval = rand(1, current($delta));
    $timename = key($delta);

    $ts = strtotime("$timeval $timename ago");
    $dt = date('Y-m-d H:i:s', $ts);

    return $dt;
}

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

function esc($content)
{
    return htmlspecialchars($content, ENT_QUOTES);
}

function formatDate(string $dateTime, string $format): string
{
    $dateTime = new DateTime($dateTime, new DateTimeZone('Europe/Moscow'));

    return $dateTime->format($format);
}

function showTitleDateFormat(string $dateTime): string
{
    $dateTime = new DateTime($dateTime, new DateTimeZone('Europe/Moscow'));

    return $dateTime->format('d-m-Y H:i');
}

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

function preparedQuery($db, string $sql, array $params)
{
    $types = str_repeat('s', count($params));
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt;
}

function sqlSelect($db, string $sql, array $params = null)
{
    if (!$params) {
        return $db->query($sql);
    }

    return preparedQuery($db, $sql, $params)->get_result();
}

function sqlGetSingle($db, string $sql, array $params = null)
{
    return sqlSelect($db, $sql, $params)->fetch_assoc();
}

function sqlGetMany($db, string $sql, array $params = null)
{
    return sqlSelect($db, $sql, $params)->fetch_all(MYSQLI_ASSOC);
}

function getQueryString(array $queryString, array $modifier): string
{
    $mergedArray = array_merge($queryString, $modifier);

    return array_filter($mergedArray) ? '?' . http_build_query($mergedArray) : '/';
}

function get404StatusCode()
{
    http_response_code(404);
    exit();
}

function getPostVal($name)
{
    return $_POST[$name] ?? "";
}
