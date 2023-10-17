<?php
function getUrl($connect, $params) // ищет url в базе, если есть, то перенаправляет, иначе - ошибка
{
    $short_key = $params[0];

    $full_url = findByKey($connect, $short_key)->url;

    if ($full_url) {
        header("Location: $full_url");
    } else
        setError(404);
}

function postUrl($connect, $key_length) //проверяет данные и записывает а БД
{
    // сохраняем данные из тела запроса
    $JSONdata = file_get_contents('php://input');
    $data = json_decode($JSONdata, true);

    $full_url = $data['url'];
    $short_key = $data['short_key'];

    // проверяем строку на url
    if (filter_var($full_url, FILTER_VALIDATE_URL)) {

        // проверяем url в базе
        $short_key = checkData($connect, $full_url, $short_key, $key_length);

        // добавляем запись в базу
        $result = addUrlData($connect, $full_url, $short_key);

        if ($result) {
            // возвращаем ответ
            $full_url_data = array('url' => $full_url, 'short_key' => $short_key);

            http_response_code(201);

            echo json_encode($full_url_data);
            exit;
        }
    }

    setError(400); // введен не URL
}


?>