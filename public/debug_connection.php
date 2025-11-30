<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Debug Connection</h3>";

$host = 'localhost';
$port = '5432';
$dbname = 'site';
$user = 'postgres';
$password = 'твой_пароль';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Подключено к PostgreSQL!<br>";
    
    // 1. Какая база данных?
    $stmt = $pdo->query("SELECT current_database()");
    $current_db = $stmt->fetchColumn();
    echo "Текущая база: <strong>$current_db</strong><br>";
    
    // 2. Какие таблицы есть в ЭТОЙ базе?
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Таблицы в базе $current_db: " . (count($tables) > 0 ? implode(', ', $tables) : 'НЕТ ТАБЛИЦ') . "<br>";
    
    // 3. Проверяем конкретно таблицу users
    if (in_array('users', $tables)) {
        echo "✅ Таблица users найдена!<br>";
        
        // Проверяем структуру
        $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users'");
        $columns = $stmt->fetchAll();
        echo "Структура users:<br>";
        foreach ($columns as $col) {
            echo "- {$col['column_name']} ({$col['data_type']})<br>";
        }
    } else {
        echo "❌ Таблица users НЕ найдена в этой базе!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "<br>";
}
?>
