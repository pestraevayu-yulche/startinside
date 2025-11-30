<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Явная проверка POST данных
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: registr.php?error=invalid_method");
    exit();
}

$name = trim($_POST['name'] ?? '');
$login = trim($_POST['login'] ?? '');
$pass = $_POST['pass'] ?? '';

error_log("Registration attempt - Name: '$name', Login: '$login'");

if (empty($name) || empty($login) || empty($pass)) {
    error_log("Empty fields detected");
    header("Location: registr.php?error=empty");
    exit();
}

include("dbconnect.php");

try {
    // Проверяем подключение к БД
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Проверяем существование пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        error_log("Login already taken: $login");
        header("Location: registr.php?error=login_taken");
        exit();
    }

    // Хешируем пароль
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    error_log("Password hashed successfully");

    // Создаем пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, login, password) VALUES (:name, :login, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $hashed_password);
    
    if ($stmt->execute()) {
        $lastId = $pdo->lastInsertId();
        error_log("User created successfully with ID: $lastId");
        header("Location: avtor.php?success=registered");
        exit();
    } else {
        error_log("Execute failed");
        header("Location: registr.php?error=database");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Registration PDO error: " . $e->getMessage());
    header("Location: registr.php?error=database");
    exit();
} catch (Exception $e) {
    error_log("Registration general error: " . $e->getMessage());
    header("Location: registr.php?error=database");
    exit();
}
?>