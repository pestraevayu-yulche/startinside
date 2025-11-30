<?php
// Настройки для Render PostgreSQL
$host = 'dpg-d4m4n9je5dus7383u960-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'startinside';
$user = 'startinside_user';
$password = '6wZ3kGtouLWSjavFKCzDPD3eymxObMuX';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Подключено к Render PostgreSQL!";
} catch (PDOException $e) {
    die('Ошибка подключения: ' . $e->getMessage());
}
?>
