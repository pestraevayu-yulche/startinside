<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Fix Users Table</h3>";

include("dbconnect.php");

// 1. Проверяем таблицу с кавычками
try {
    $stmt = $pdo->query('SELECT * FROM "users" LIMIT 1');
    $users = $stmt->fetchAll();
    echo "✅ Таблица 'users' (с кавычками) содержит " . count($users) . " записей<br>";
} catch (PDOException $e) {
    echo "❌ Таблица 'users' (с кавычками) недоступна: " . $e->getMessage() . "<br>";
}

// 2. Проверяем таблицу без кавычек  
try {
    $stmt = $pdo->query('SELECT * FROM users LIMIT 1');
    $users = $stmt->fetchAll();
    echo "✅ Таблица users (без кавычек) содержит " . count($users) . " записей<br>";
} catch (PDOException $e) {
    echo "❌ Таблица users (без кавычек) недоступна: " . $e->getMessage() . "<br>";
}

// 3. Проверяем структуру таблицы
try {
    $stmt = $pdo->query('SELECT column_name, data_type FROM information_schema.columns WHERE table_name = \'users\'');
    $columns = $stmt->fetchAll();
    echo "✅ Структура таблицы users:<br>";
    foreach ($columns as $col) {
        echo " - " . $col['column_name'] . " (" . $col['data_type'] . ")<br>";
    }
} catch (PDOException $e) {
    echo "❌ Ошибка получения структуры: " . $e->getMessage() . "<br>";
}

// 4. Тест регистрации с кавычками
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';
    
    echo "<hr><h4>Тест регистрации (с кавычками):</h4>";
    
    try {
        // Проверяем существование пользователя С КАВЫЧКАМИ
        $stmt = $pdo->prepare('SELECT id FROM "users" WHERE login = :login');
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "❌ Логин '$login' уже занят<br>";
        } else {
            // Регистрируем С КАВЫЧКАМИ
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO "users" (name, login, password) VALUES (:name, :login, :password)');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                echo "✅ Регистрация успешна!<br>";
            } else {
                echo "❌ Ошибка при регистрации<br>";
            }
        }
    } catch (PDOException $e) {
        echo "❌ Ошибка: " . $e->getMessage() . "<br>";
    }
}
?>

<form method="post">
    <input type="text" name="name" placeholder="Имя" required><br>
    <input type="text" name="login" placeholder="Логин" required><br>
    <input type="password" name="pass" placeholder="Пароль" required><br>
    <button type="submit">Тест регистрации</button>
</form>
