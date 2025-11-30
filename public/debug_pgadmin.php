<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Debug PostgreSQL Connection</h3>";

// Попробуй разные настройки подключения
$configs = [
    [
        'host' => 'localhost',
        'port' => '5432', 
        'dbname' => 'site',
        'user' => 'postgres',
        'password' => '1234',
        'desc' => 'Основные настройки'
    ],
    [
        'host' => '127.0.0.1',
        'port' => '5432',
        'dbname' => 'site', 
        'user' => 'postgres',
        'password' => '1234',
        'desc' => 'Через IP 127.0.0.1'
    ]
];

foreach ($configs as $config) {
    echo "<h4>Пробуем: {$config['desc']}</h4>";
    echo "Хост: {$config['host']}:{$config['port']}, БД: {$config['dbname']}, Пользователь: {$config['user']}<br>";
    
    try {
        $pdo = new PDO(
            "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}", 
            $config['user'], 
            $config['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ УСПЕХ! Подключено!<br>";
        
        // Проверяем таблицы
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Таблицы: " . (count($tables) > 0 ? implode(', ', $tables) : 'НЕТ ТАБЛИЦ') . "<br>";
        
        break; // Если подключились - выходим
        
    } catch (PDOException $e) {
        echo "❌ Ошибка: " . $e->getMessage() . "<br><br>";
    }
}
?>
