<?php


// выполнение запросов
function executeSQL($connect, $sql, $sql_params) //выполняет запрос с параметрами
{
    $stmt = $connect->prepare($sql);

    $stmt->execute($sql_params);

    return $stmt;
}

function getDataSQL($connect, $sql, $sql_params) // выполняет запрос с параметрами и возвращает результат
{
    $stmt = executeSQL($connect, $sql, $sql_params);

    $element = $stmt->fetch(PDO::FETCH_OBJ);

    return $element;
}

// взаимодействие с БД
function findByKey($connect, $short_key) // поиск url по ключу
{
    $sql = 'SELECT u.url FROM `urls` u WHERE u.short_key = :short_key limit 1;';

    $sql_params = array(':short_key' => $short_key);

    return getDataSQL($connect, $sql, $sql_params);
}

function findByUrl($connect, $full_url) // поиск по url
{
    $sql_url = 'SELECT u.url, u.short_key FROM `urls` u WHERE u.url = :full_url limit 1;';

    $sql_url_params = array(':full_url' => $full_url);

    return getDataSQL($connect, $sql_url, $sql_url_params);
}

function addUrlData($connect, $full_url, $short_key) // добавление записи
{
    if (strlen($short_key) <= 20 && strlen($short_key) > 0) {
        $sql = 'INSERT INTO `urls` (`id`, `url`, `short_key`) VALUES (NULL, :full_url, :short_key);';

        $sql_params = array(':full_url' => $full_url, ':short_key' => $short_key);

        return executeSQL($connect, $sql, $sql_params);

    }
    return 0;
}

//Обработка ошибок
function setError($code)
{
    http_response_code($code);
    switch ($code) {
        case 400:
            $message = $GLOBALS['ERROR_MESSAGE400'];
            break;
        case 404:
            $message = $GLOBALS['ERROR_MESSAGE404'];
            break;
        case 409:
            $message = $GLOBALS['ERROR_MESSAGE409'];
            break;
        default:
            $message = $GLOBALS['ERROR_MESSAGE'];
    }
    echo json_encode(array('message' => $message));
    exit;
}

// Генерация ключа
function genKey($length, $condition)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $short_key = '';

    do {
        for ($i = 0; $i < $length; $i++) {
            $short_key .= $characters[rand(0, strlen($characters) - 1)];
        }
    } while ($condition);

    return $short_key;
}

// Проверка данных
function checkData($connect, $full_url, $short_key, $key_length)
{
    // ищем url в базе
    $full_url_data = findByUrl($connect, $full_url);

    // Если был введен кастомный ключ и он уже привязан к этому URL, то возвращаем этот кастомный ключ и выходим.
    // Если url уже в базе и кастомный ключ не введен, то возвращаем уже имеющийся в базе ключ и выходим.
    // Если был введен кастомный ключ и он уже привязан к другому URL, то ошибка.
    // Если url нет в базе и кастомный ключ не введнен, то генерим ключ. 
    if ($full_url_data && !$short_key || (findByKey($connect, $short_key)->url == $full_url)) {
        http_response_code(200);
        $full_url_data = array('url' => $full_url, 'short_key' => $short_key ? $short_key : $full_url_data->short_key);
        echo json_encode($full_url_data);
        exit;
        // Генерируем уникальный случайный ключ
    } else if (!$short_key) {
        $short_key = genKey($key_length, findByKey($connect, $short_key));
        //  ошибка
    } else if (findByKey($connect, $short_key) || $short_key == 'frontend')
        setError(409);

    return $short_key; // возвращаем сгенерированный или введенный ключ
}
?>