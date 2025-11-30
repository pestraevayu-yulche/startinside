<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if (empty($login) || empty($pass)) {
        header("Location: avtor.php?error=empty");
        exit();
    }

    include("dbconnect.php");

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch();

        if (empty($user)) {
            header("Location: avtor.php?error=invalid");
            exit();
        } else {
            if (password_verify($pass, $user['password'])) {
                $_SESSION['login'] = $user['login'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("Location: index.php");
                exit();
            } else {
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
    header("Location: avtor.php");
    exit();
}
?>
