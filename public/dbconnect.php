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
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['error'] = 'Database connection failed';
    }
}
?>
