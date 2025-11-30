<?php
include(__DIR__ . '/tpl/header.php');

// Подключаем базу данных
include("dbconnect.php");
?>
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/programs.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php
    include(__DIR__ . '/tpl/nav.php');
    ?>
    
    <!-- Секция направления -->
    <div class="directions-section" style="padding: 40px 0; min-height: 70vh;">
        <div class="container">
            <?php
            // Получаем ID направления из URL - исправлено на 'direction'
            $direction_id = false;
            if (!empty($_GET['direction'])) {
                $direction_id = $_GET['direction'];
            }
            // Также проверяем старый параметр 'id' для обратной совместимости
            elseif (!empty($_GET['id'])) {
                $direction_id = $_GET['id'];
            }
            
            // Если ID передан, получаем информацию о направлении
            if ($direction_id) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM directions WHERE id = :id");
                    $stmt->bindParam(':id', $direction_id);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        $direction = $stmt->fetch();
                        ?>
                        <h1 class="section-title"><?php echo htmlspecialchars($direction['name']); ?></h1>
                        <p class="section-subtitle">Подробная информация о направлении и тестировании</p>
                        
                        <div class="direction-detail-info">
                            <div class="direction-description">
                                <h3>Описание направления</h3>
                                <p><?php echo htmlspecialchars($direction['description']); ?></p>
                                
                                <h4>Оцениваемые навыки:</h4>
                                <p><?php echo htmlspecialchars($direction['skills']); ?></p>
                                
                                <h4>Возможные профессии:</h4>
                                <p><?php echo htmlspecialchars($direction['career_paths']); ?></p>
                            </div>
                            
                            <div class="action-buttons" style="margin-top: 50px; margin-bottom: 50px; text-align: center;">
                                <button class="btn btn-primary btn-lg subscribe-btn" data-bs-toggle="modal" data-bs-target="#calculationModal" style="margin: 10px;">
                                    Купить подписку
                                </button>
                                <a href="directions.php" class="btn btn-outline-secondary btn-lg" style="margin: 10px;">
                                    Вернуться к направлениям
                                </a>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo '<div class="alert alert-danger">Направление не найдено</div>';
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching direction: " . $e->getMessage());
                    echo '<div class="alert alert-danger">Ошибка при загрузке направления</div>';
                }
            } else {
                echo '<div class="alert alert-warning">Не указано направление. Переданные параметры: ';
                echo htmlspecialchars(print_r($_GET, true));
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Модальное окно для покупки подписки -->
    <div class="modal fade" id="calculationModal" tabindex="-1" aria-labelledby="calculationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calculationModalLabel">Оформление подписки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="subscription-info">
                        <h6>Выбранный тариф:</h6>
                        <p><strong>Для одного человека</strong> - ₽2,500 в месяц</p>
                        
                        <div class="benefits-list">
                            <h6>Включено в подписку:</h6>
                            <ul>
                                <li>Полный доступ ко всем направлениям тестирования</li>
                                <li>Детальный анализ способностей</li>
                                <li>Персональный отчет с рекомендациями</li>
                                <li>Поддержка 24/7</li>
                                <li>Доступ на 1 месяц</li>
                            </ul>
                        </div>
                        
                        <form id="subscriptionForm">
                            <div class="mb-3">
                                <label class="form-label">Ваш email:</label>
                                <input type="email" class="form-control" id="userEmail" required style="color: #333;" placeholder="example@mail.ru">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Телефон для связи:</label>
                                <input type="tel" class="form-control" id="userPhone" required style="color: #333;" placeholder="+7 (XXX) XXX-XX-XX">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-success" onclick="processSubscription()">Оформить подписку</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function processSubscription() {
            const email = document.getElementById('userEmail').value;
            const phone = document.getElementById('userPhone').value;
            
            if (!email || !phone) {
                alert('Пожалуйста, заполните все поля');
                return;
            }
            
            // Здесь будет логика обработки подписки
            alert('Подписка успешно оформлена! Мы свяжемся с вами в ближайшее время для подтверждения.');
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('calculationModal'));
            modal.hide();
            
            // Очищаем форму
            document.getElementById('userEmail').value = '';
            document.getElementById('userPhone').value = '';
        }
    </script>

<?php
include(__DIR__ . '/tpl/footer.php');

?>
