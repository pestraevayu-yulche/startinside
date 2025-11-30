<?php
// Подключаем файл соединения с базой данных
include("dbconnect.php");

// Устанавливаем заголовок для JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Выполняем запрос к базе данных для получения направлений
    $stmt = $pdo->query("SELECT * FROM directions ORDER BY id");
    $directions = $stmt->fetchAll();
    
    // Возвращаем данные в формате JSON
    echo json_encode($directions);
    
} catch (PDOException $e) {
    // В случае ошибки возвращаем пустой массив
    echo json_encode([]);
}
?>