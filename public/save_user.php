<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['name'])) $name = trim($_POST['name']);
if (isset($_POST['login'])) $login = trim($_POST['login']);
if (isset($_POST['pass'])) $pass = $_POST['pass'];

if (empty($name) || empty($login) || empty($pass)) {
    header("Location: registr.php?error=empty");
    exit();
}

include("dbconnect.php");

try {
    // Проверяем существование пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        header("Location: registr.php?error=login_taken");
        exit();
    }

    // Хешируем пароль и создаем пользователя
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, login, password) VALUES (:name, :login, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $hashed_password);
    
    if ($stmt->execute()) {
        header("Location: avtor.php?success=registered");
        exit();
    } else {
        header("Location: registr.php?error=database");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    header("Location: registr.php?error=database");
    exit();
}
?>
