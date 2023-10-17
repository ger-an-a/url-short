<?php

header('Content-Type: application/json; charset=utf-8');

// константы
$ERROR_MESSAGE400 = 'Некорректный URL или ключ';
$ERROR_MESSAGE404 = 'URL не найден';
$ERROR_MESSAGE409 = 'Ключ занят';
$ERROR_MESSAGE = 'Ошибка';

$key_length = 6;

require 'controllers.php';
require 'functions.php';

//подключение к базе. $db, $user, $password хранятся в файле
require 'config/config.php';

try {
    $connect = new PDO($db, $user, $password);
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

//создание таблицы, если её нет
executeSQL($connect, file_get_contents("initial.sql"), array());

//определяем метод и параметры запроса
$method = $_SERVER['REQUEST_METHOD'];
$url = $_GET['url'];
$params = explode('/', $url);

//обрабатываем запрос
switch ($method) {
    case 'GET':
        getUrl($connect, $params);
        break;

    case 'POST':
        postUrl($connect, $key_length);
        break;
}

?>