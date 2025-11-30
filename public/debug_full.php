<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Full Debug</h3>";

include("dbconnect.php");

// 1. Проверяем подключение к БД
try {
    $pdo->query("SELECT 1");
    echo "✅ PostgreSQL подключен<br>";
} catch (PDOException $e) {
    echo "❌ Ошибка PostgreSQL: " . $e->getMessage() . "<br>";
    exit();
}

// 2. Проверяем таблицу users и данные
try {
    $stmt = $pdo->query("SELECT * FROM users LIMIT 5");
    $users = $stmt->fetchAll();
    echo "✅ Таблица users содержит " . count($users) . " записей<br>";
    
    if (count($users) > 0) {
        echo "Пример пользователя: ";
        print_r($users[0]);
    }
} catch (PDOException $e) {
    echo "❌ Ошибка чтения users: " . $e->getMessage() . "<br>";
}

// 3. Тестируем регистрацию
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';
    
    echo "<hr><h4>Тест регистрации:</h4>";
    echo "Данные: name='$name', login='$login', pass='$pass'<br>";
    
    try {
        // Проверяем существование пользователя
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "❌ Логин '$login' уже занят<br>";
        } else {
            // Регистрируем
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            echo "Хеш пароля: " . $hashed_password . "<br>";
            
            $stmt = $pdo->prepare("INSERT INTO users (name, login, password) VALUES (:name, :login, :password)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                $user_id = $pdo->lastInsertId();
                echo "✅ Регистрация успешна! ID: $user_id<br>";
                
                // Проверяем что записалось
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id);
                $stmt->execute();
                $new_user = $stmt->fetch();
                echo "Данные в БД: ";
                print_r($new_user);
            } else {
                echo "❌ Ошибка при выполнении INSERT<br>";
            }
        }
    } catch (PDOException $e) {
        echo "❌ Ошибка БД: " . $e->getMessage() . "<br>";
    }
}

// 4. Тестируем авторизацию
if (isset($_POST['test_login'])) {
    $login = $_POST['test_login'] ?? '';
    $pass = $_POST['test_pass'] ?? '';
    
    echo "<hr><h4>Тест авторизации:</h4>";
    echo "Данные: login='$login', pass='$pass'<br>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Пользователь найден: " . $user['name'] . "<br>";
            echo "Пароль в БД: " . $user['password'] . "<br>";
            
            if (password_verify($pass, $user['password'])) {
                echo "✅ Пароль верный!<br>";
            } else {
                echo "❌ Пароль неверный!<br>";
            }
        } else {
            echo "❌ Пользователь не найден<br>";
        }
    } catch (PDOException $e) {
        echo "❌ Ошибка авторизации: " . $e->getMessage() . "<br>";
    }
}
?>

<hr>
<h4>Тест регистрации:</h4>
<form method="post">
    <input type="text" name="name" placeholder="Имя" required><br>
    <input type="text" name="login" placeholder="Логин" required><br>
    <input type="password" name="pass" placeholder="Пароль" required><br>
    <button type="submit">Тест регистрации</button>
</form>

<hr>
<h4>Тест авторизации:</h4>
<form method="post">
    <input type="text" name="test_login" placeholder="Логин" required><br>
    <input type="password" name="test_pass" placeholder="Пароль" required><br>
    <button type="submit">Тест авторизации</button>
</form>
