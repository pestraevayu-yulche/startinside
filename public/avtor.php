<?php 
session_start();
// Проверяем, не авторизован ли уже пользователь
if (isset($_SESSION['login']) && isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация - СтартИнсайт</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="background-container">
        <img src="img/log.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">СтартИнсайт</a>
        </div>
    </nav>

    <div class="hero-section">
        <div class="auth-container">
            <div class="auth-card">
                <h2 class="auth-title">Вход в СтартИнсайт</h2>
                
                <!-- Блок для отображения ошибок -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        switch ($_GET['error']) {
                            case 'empty':
                                echo 'Пожалуйста, заполните все поля';
                                break;
                            case 'invalid':
                                echo 'Неверный логин или пароль. Попробуйте еще раз.';
                                break;
                            default:
                                echo 'Произошла ошибка при авторизации';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Блок для отображения успешных сообщений -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        switch ($_GET['success']) {
                            case 'registered':
                                echo 'Регистрация прошла успешно! Теперь вы можете войти в систему.';
                                break;
                            case 'logout':
                                echo 'Вы успешно вышли из системы.';
                                break;
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="test_user.php" method="post">
                    <div class="mb-3">
                        <input type="text" name="login" class="form-control auth-input" placeholder="Логин" required value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="pass" class="form-control auth-input" placeholder="Пароль" required>
                    </div>
                    <button type="submit" class="submit-btn">Войти</button>
                </form>
                <p class="auth-link">Нет аккаунта? <a href="registr.php">Зарегистрироваться</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>