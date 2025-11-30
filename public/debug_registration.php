<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Debug Registration</h3>";

include("dbconnect.php");

// Проверяем подключение к БД
try {
    $test = $pdo->query("SELECT 1");
    echo "✅ PostgreSQL подключен<br>";
} catch (PDOException $e) {
    echo "❌ Ошибка PostgreSQL: " . $e->getMessage() . "<br>";
    exit();
}

// Проверяем таблицу users
try {
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'users'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Таблица users существует. Колонки: " . implode(', ', $columns) . "<br>";
} catch (PDOException $e) {
    echo "❌ Таблица users не найдена: " . $e->getMessage() . "<br>";
}

// Тестируем регистрацию
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';
    
    echo "Данные формы: name=$name, login=$login, pass=$pass<br>";
    
    try {
        // Проверяем существование пользователя
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "❌ Логин уже занят<br>";
        } else {
            // Регистрируем
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, login, password) VALUES (:name, :login, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                echo "✅ Регистрация успешна!<br>";
                echo "ID нового пользователя: " . $pdo->lastInsertId() . "<br>";
            } else {
                echo "❌ Ошибка при вставке<br>";
            }
        }
    } catch (PDOException $e) {
        echo "❌ Ошибка БД: " . $e->getMessage() . "<br>";
    }
}
?>

<form method="post">
    <input type="text" name="name" placeholder="Имя" required><br>
    <input type="text" name="login" placeholder="Логин" required><br>
    <input type="password" name="pass" placeholder="Пароль" required><br>
    <button type="submit">Тест регистрации</button>
</form>
