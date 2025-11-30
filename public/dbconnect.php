<?php
// Подключение к PostgreSQL на Render
$host = 'dpg-d4m4n9je5dus7383u960-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'startinside';
$user = 'startinside_user';
$password = '6wZ3kGtouLWSjavFKCzDPD3eymxObMuX';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Для отладки
    error_log("Успешное подключение к PostgreSQL на Render");
    
} catch (PDOException $e) {
    error_log("Ошибка подключения к БД: " . $e->getMessage());
    die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
}
?>