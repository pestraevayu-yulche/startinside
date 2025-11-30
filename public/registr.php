<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - СтартИнсайт</title>
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
                <h2 class="auth-title">Регистрация в СтартИнсайт</h2>
                
                <!-- Блок для отображения ошибок -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        switch ($_GET['error']) {
                            case 'empty':
                                echo 'Пожалуйста, заполните все поля';
                                break;
                            case 'login_taken':
                                echo 'Этот логин уже занят. Выберите другой.';
                                break;
                            case 'database':
                                echo 'Произошла ошибка при регистрации. Попробуйте еще раз.';
                                break;
                            default:
                                echo 'Произошла ошибка при регистрации';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="save_user.php" method="post">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control auth-input" placeholder="Ваше имя" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="login" class="form-control auth-input" placeholder="Логин" required value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="pass" class="form-control auth-input" placeholder="Пароль" required>
                    </div>
                    <button type="submit" class="submit-btn">Зарегистрироваться</button>
                </form>
                <p class="auth-link">Уже есть аккаунт? <a href="avtor.php">Войти</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>