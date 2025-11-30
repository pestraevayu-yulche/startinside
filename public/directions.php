<?php
include(__DIR__ . '/tpl/header.php');
?>
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/programs.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php
    include(__DIR__ . '/tpl/nav.php');
    ?>
    
    <!-- Секция направлений с React -->
    <div class="directions-section">
        <div class="container">
            <h1 class="section-title">Выберите IT-направление</h1>
            <p class="section-subtitle">Пройдите тестирование по выбранной профессии и определите ваш уровень</p>
            
            <!-- Передаем информацию об авторизации -->
            <script>
                window.isLoggedIn = <?php 
                    $isLogged = isset($_SESSION['login']) && isset($_SESSION['id']) ? 'true' : 'false';
                    echo $isLogged; 
                ?>;
            </script>
            
            <!-- Область для React компонента -->
            <div id="react-directions"></div>
        </div>
    </div>

    <!-- Секция расчета стоимости -->
    <div class="pricing-section">
        <div class="container">
            <h2 class="pricing-title">Рассчитайте стоимость подписки</h2>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="pricing-card">
                        <h3 class="plan-name">Для одного человека</h3>
                        <div class="plan-price">2,500 руб./месяц</div>
                        <ul class="plan-features">
                            <li>Полный анализ способностей</li>
                            <li>Персональный отчет</li>
                            <li>Рекомендации по развитию</li>
                            <li>Поддержка 24/7</li>
                        </ul>
                        <button class="calculate-btn" data-bs-toggle="modal" data-bs-target="#calculationModal" data-plan-type="individual">
                            Рассчитать стоимость
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="pricing-card">
                        <h3 class="plan-name">Для компании</h3>
                        <div class="plan-price">2,800 руб./сотрудник</div>
                        <ul class="plan-features">
                            <li>Полный анализ для сотрудников</li>
                            <li>Командный отчет</li>
                            <li>Рекомендации по распределению ролей</li>
                            <li>Приоритетная поддержка</li>
                        </ul>
                        <button class="calculate-btn" data-bs-toggle="modal" data-bs-target="#calculationModal" data-plan-type="company">
                            Рассчитать стоимость
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальные окна -->
    <div class="modal fade" id="calculationModal" tabindex="-1" aria-labelledby="calculationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calculationModalLabel">Расчет стоимости подписки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="subscriptionForm">
                        <div class="mb-3">
                            <label class="form-label">Тип подписки:</label>
                            <input type="text" class="form-control" id="subscriptionType" readonly style="color: #333; background-color: #f8f9fa;">
                        </div>
                        <div class="mb-3">
                        <label class="form-label">Количество сотрудников:</label>
                            <input type="number" 
                                class="form-control" 
                                id="userCount" 
                                name="userCount"
                                min="1" 
                                max="1000" 
                                value="1"
                                placeholder="Введите количество сотрудников"
                                style="color: #333;">
                            </div>
                        <div class="mb-3">
                            <label class="form-label">Период подписки:</label>
                            <select class="form-select" id="subscriptionPeriod" style="color: #333;">
                                <option value="1">1 месяц</option>
                                <option value="6">6 месяцев</option>
                                <option value="12">12 месяцев</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ваш email:</label>
                            <input type="email" class="form-control" id="userEmail" required style="color: #333;">
                        </div>
                    </form>
                    <div id="calculationResult" class="mt-3 p-3 bg-light rounded" style="display: none;">
                        <h6 style="color: #333;">Результат расчета:</h6>
                        <p id="resultText" style="color: #333;"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" onclick="calculateSubscription()">Рассчитать</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для требования авторизации -->
    <div class="modal fade" id="authRequiredModal" tabindex="-1" aria-labelledby="authRequiredModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="authRequiredModalLabel">Требуется авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="color: #333;">Для прохождения тестирования необходимо войти в систему или зарегистрироваться.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <a href="avtor.php" class="btn btn-primary">Войти</a>
                    <a href="registr.php" class="btn btn-success">Зарегистрироваться</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключаем React компонент -->
    <script type="text/babel" src="/js/directions-my.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Обработчик для кнопок расчета стоимости
    document.addEventListener('DOMContentLoaded', function() {
        const calculateButtons = document.querySelectorAll('.calculate-btn');
        calculateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const planType = this.getAttribute('data-plan-type');
                setupModal(planType);
            });
        });
    });

    function setupModal(planType) {
        if (planType === 'individual') {
            document.getElementById('subscriptionType').value = 'Для одного человека';
            document.getElementById('userCount').value = '1';
            document.getElementById('userCount').disabled = true;
        } else {
            document.getElementById('subscriptionType').value = 'Для компании';
            document.getElementById('userCount').value = '5';
            document.getElementById('userCount').disabled = false;
        }
        
        document.getElementById('calculationResult').style.display = 'none';
        document.getElementById('userEmail').value = '';
    }

    function calculateSubscription() {
        const userCount = parseInt(document.getElementById('userCount').value);
        const period = parseInt(document.getElementById('subscriptionPeriod').value);
        const email = document.getElementById('userEmail').value;
        const subscriptionType = document.getElementById('subscriptionType').value;

        // Валидация данных
        if (!email) {
            alert('Пожалуйста, введите ваш email');
            return;
        }

        if (userCount < 1) {
            alert('Количество сотрудников должно быть не менее 1');
            return;
        }

        let basePrice = 0;
        let totalPrice = 0;

        if (subscriptionType === 'Для одного человека') {
            basePrice = 2500;
            totalPrice = basePrice * period;
        } else {
            basePrice = 2800;
            totalPrice = basePrice * period * userCount; // Исправлено: userCount вместо user_count
        }

        totalPrice = Math.round(totalPrice);

        document.getElementById('resultText').innerHTML = 
            `Стоимость вашей подписки: <strong>${totalPrice.toLocaleString('ru-RU')} ₽</strong><br>
            Количество пользователей: ${userCount}<br>
            Период: ${period} месяцев<br>
            Мы свяжемся с вами по адресу: ${email}`;

        document.getElementById('calculationResult').style.display = 'block';
        
        // Прокрутка к результату
        document.getElementById('calculationResult').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'nearest' 
        });
    }

    // Добавляем обработчик нажатия Enter в поле email
    document.getElementById('userEmail').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            calculateSubscription();
        }
    });
    </script>
<?php
include(__DIR__ . '/tpl/footer.php');

?>

