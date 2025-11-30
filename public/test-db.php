<?php
include('dbconnect.php');

echo "<h1>Тест подключения к БД</h1>";

try {
    // Проверяем подключение
    echo "✅ Подключение к PostgreSQL успешно<br>";
    
    // Проверяем таблицы
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll();
    
    echo "<h2>Таблицы в базе:</h2>";
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "✅ " . $table['table_name'] . "<br>";
        }
    } else {
        echo "❌ Таблицы не найдены<br>";
    }
    
    // Проверяем направления
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM directions");
    $result = $stmt->fetch();
    echo "<h2>Направления:</h2>";
    echo "Количество: " . $result['count'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Ошибка БД: " . $e->getMessage();
}
?>
