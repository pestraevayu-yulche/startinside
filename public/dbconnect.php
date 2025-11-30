<?php
// Проверяем, на Render ли мы
if (getenv('RENDER')) {
    // Настройки для Render
    $host = 'dpg-d4m4n9je5dus7383u960-a.oregon-postgres.render.com';
    $port = '5432';
    $dbname = 'startinside';
    $user = 'startinside_user';
    $password = '6wZ3kGtouLWSjavFKCzDPD3eymxObMuX';
} else {
    // Локальные настройки (для тестирования)
    $host = 'localhost';
    $port = '5432';
    $dbname = 'site';
    $user = 'postgres';
    $password = '1234';
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Connect Error: ' . $e->getMessage());
}
?>
