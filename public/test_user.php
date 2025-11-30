<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';

    // Проверяем заполнение полей
    if (empty($login) || empty($pass)) {
        header("Location: avtor.php?error=empty");
        exit();
    }

    include("dbconnect.php");

    try {
        // Ищем пользователя
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch();

        if (empty($user)) {
            // Пользователь не найден
            header("Location: avtor.php?error=invalid");
            exit();
        } else {
            // Проверяем пароль
            if (password_verify($pass, $user['password'])) {
                // Успешная авторизация
                $_SESSION['login'] = $user['login'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("Location: index.php");
                exit();
            } else {
                // Неверный пароль
                header("Location: avtor.php?error=invalid");
                exit();
            }
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: avtor.php?error=database");
        exit();
    }
} else {
    // Если кто-то попытался зайти напрямую
    header("Location: avtor.php");
    exit();
}
?>