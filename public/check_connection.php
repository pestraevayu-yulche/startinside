<?php
include('dbconnect.php');

// Этот файл только для проверки - не использовать в основном коде
try {
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✅ PostgreSQL подключен! Версия: $version<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM directions");
    $count = $stmt->fetchColumn();
    echo "✅ Направлений в базе: $count<br>";
    
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>
