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
function db_get_prepare_stmt(string $link, string $sql, $data = [])
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
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
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
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template (string $name, array $data = []): string {
    $name = 'view/templates/' . $name;

    ob_start();
    extract($data);
    require $name;

    return ob_get_clean();
}

/**
 * Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 *
 * @return string Ошибку если валидация не прошла
 */
function check_youtube_url(string $url)
{
    $id = extract_youtube_id($url);

    set_error_handler(function () {}, E_WARNING);
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $id);
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
 * @param string $youtube_url Ссылка на youtube видео
 * @return string
 */
function embed_youtube_video (string $youtube_url): string
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = "https://www.youtube.com/embed/" . $id;
        $res = '<iframe width="760" height="400" src="' . $src . '" frameborder="0"></iframe>';
    }

    return $res;
}

/**
 * Возвращает img-тег с обложкой видео для вставки на страницу
 * @param string $youtube_url Ссылка на youtube видео
 * @return string
 */
function embed_youtube_cover (string $youtube_url): string
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = sprintf("https://img.youtube.com/vi/%s/mqdefault.jpg", $id);
        $res = '<img alt="youtube cover" width="320" height="120" src="' . $src . '" />';
    }

    return $res;
}

/**
 * Извлекает из ссылки на youtube видео его уникальный ID
 * @param string $youtube_url Ссылка на youtube видео
 * @return array
 */
function extract_youtube_id (string $youtube_url)
{
    $id = false;

    $parts = parse_url($youtube_url);

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
function generate_random_date($index)
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

    return $dt = date('Y-m-d H:i:s', $ts);
}

function crop_text (string $text, int $max_chars = 300): string {
    if (mb_strlen($text) < $max_chars) {
        return $text;
    }

    $text_parts = explode(' ', $text);
    $total_chars = 0;
    $space_value = 1;
    $verified_text = array();

    foreach ($text_parts as $text_part) {
        $total_chars += mb_strlen($text_part) + $space_value;

        if (($total_chars - $space_value) >= $max_chars) {
            break;
        }

        $verified_text[] = $text_part;
    }

    $text = implode(' ', $verified_text);

    return $text . '...';
}

function show_title_date_format (string $date_time): string {
    $date_time = new DateTime($date_time, new DateTimeZone('Europe/Moscow'));
    return esc($date_time->format('d-m-Y H:i'));
}

function get_relative_date_format (string $post_date, string $string_end): string {
    $post_date = new DateTime($post_date, new DateTimeZone('Europe/Moscow'));
    $current_date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $date_time_diff = $post_date->diff($current_date);
    $correct_date_format = '';

    if ($date_time_diff->y !== 0) {
        $years = $date_time_diff->y;
        $correct_date_format = "{$years} " . get_noun_plural_form($years, 'год', 'года', 'лет') . " $string_end";
    } elseif ($date_time_diff->m !== 0) {
        $months = $date_time_diff->m;
        $correct_date_format = "{$months} " . get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев') . " $string_end";
    } elseif ($date_time_diff->d >= 7) {
        $weeks = floor($date_time_diff->d / 7);
        $correct_date_format = "{$weeks} " . get_noun_plural_form($weeks, 'неделю', 'недели', 'недели') . " $string_end";
    } elseif ($date_time_diff->d < 7 && $date_time_diff->d !== 0) {
        $days = $date_time_diff->d;
        $correct_date_format = "{$days} " . get_noun_plural_form($days, 'день', 'дня', 'дней') . " $string_end";
    } elseif ($date_time_diff->h !== 0) {
        $hours = $date_time_diff->h;
        $correct_date_format = "{$hours} " . get_noun_plural_form($hours, 'час', 'часа', 'часов') . " $string_end";
    } elseif ($date_time_diff->i !== 0) {
        $minutes = $date_time_diff->i;
        $correct_date_format = "{$minutes} " . get_noun_plural_form($minutes, 'минуту', 'минуты', 'минут') . " $string_end";
    }

    return esc($correct_date_format);
}

function esc ($content): string {
    return htmlspecialchars($content, ENT_QUOTES);
}

function prepared_query ($db, $sql, $params) {
    $types = str_repeat('s', count($params));
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt;
}

function sql_select ($db, $sql, $params = []) {
    if (!$params) {
        return $db->query($sql);
    }
    return prepared_query($db, $sql, $params)->get_result();
}

function sql_get_single ($db, $sql, $params = []) {
    return sql_select($db, $sql, $params)->fetch_assoc();
}

function sql_get_many ($db, $sql, $params = []) {
    return sql_select($db, $sql, $params)->fetch_all(MYSQLI_ASSOC);
}

function get_path (bool $is_photo, $file_name): string {
    return !$is_photo
        ? "/view/img" . "/users/" . $file_name
        : "/view/img" . "/photos/" . $file_name;
}

// TODO попробовать скомпоновать в 1 с get_404_page
function build_404_page ($is_auth, $user_name): string {
    http_response_code(404);

    $page_layout = include_template('layout.php', [
        'page_title' => 'Readme ▶️ 404',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'page_main_content' => include_template('404.php'),
    ]);

    return print $page_layout;
}

function get_404_page ($is_auth, $user_name) {
    build_404_page($is_auth, $user_name);
    exit();
}

function get_query_string (array $query_string, array $modifier):string {
    // TODO избавится от $merged_array
    $merged_array = array_merge($query_string, $modifier);
    foreach ($merged_array as $key => $value) {
        if ($value === null) {
            unset($merged_array[$key]);
        }
    }
    return $merged_array ? '?' . http_build_query($merged_array) : '/';
}

