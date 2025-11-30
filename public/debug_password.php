<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Debug Password & Connection</h3>";

// Попробуй разные пароли
$passwords_to_try = [
    'пароль_который_используешь_в_pgAdmin',
    'postgres', // стандартный пароль
    'password',
    '1234',
    '12345',
    '123456',
    '' // пустой пароль
];

foreach ($passwords_to_try as $password) {
    echo "<h4>Пробуем пароль: '" . ($password ? $password : 'пустой') . "'</h4>";
    
    try {
        $pdo = new PDO(
            "pgsql:host=localhost;port=5432;dbname=site", 
            'postgres', 
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ УСПЕХ! Подключились с этим паролем!<br>";
        
        // Проверяем таблицы
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Таблицы: " . implode(', ', $tables) . "<br>";
        
        break;
        
    } catch (PDOException $e) {
        echo "❌ Ошибка: " . $e->getMessage() . "<br><br>";
    }
}
?>
