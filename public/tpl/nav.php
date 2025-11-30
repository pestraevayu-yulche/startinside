<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">СтартИнсайт</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="directions.php">Направления</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacts.php">Контакты</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
                    <!-- Простые ссылки вместо выпадающего меню -->
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><?php echo htmlspecialchars($_SESSION['name']); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_progress.php">Мой прогресс</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="exit.php">Выйти</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="avtor.php">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registr.php">Регистрация</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>