<?php
session_start();
if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
    header("Location: avtor.php");
    exit();
}
include("dbconnect.php");

// Получаем данные пользователя
$user_id = $_SESSION['id'];
$user_data = array();
$profile_data = array();

try {
    // Основные данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_data = $stmt->fetch();
    
    // Данные профиля
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $profile_data = $stmt->fetch();
    }
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

// Получаем расширенные результаты soft skills теста
$detailed_soft_skills = array();
try {
    $stmt = $pdo->prepare("SELECT * FROM soft_skills_detailed_results WHERE user_id = :user_id ORDER BY test_date DESC LIMIT 1");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $detailed_soft_skills = $stmt->fetch();
    }
} catch (PDOException $e) {
    error_log("Error fetching detailed soft skills results: " . $e->getMessage());
}

// Получаем результаты технических тестов
$technical_results = array();
try {
    $stmt = $pdo->prepare("SELECT tr.*, d.name as direction_name 
                          FROM test_results tr 
                          LEFT JOIN directions d ON tr.direction_id = d.id 
                          WHERE tr.user_id = :user_id 
                          ORDER BY tr.test_date DESC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $technical_results = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    error_log("Error fetching technical results: " . $e->getMessage());
}

// Функция для получения уровня навыка
function getSkillLevel($score) {
    if ($score >= 4.5) return ['level' => 'Эксперт', 'color' => '#10b981'];
    if ($score >= 3.5) return ['level' => 'Продвинутый', 'color' => '#3b82f6'];
    if ($score >= 2.5) return ['level' => 'Средний', 'color' => '#f59e0b'];
    if ($score >= 1.5) return ['level' => 'Начинающий', 'color' => '#ef4444'];
    return ['level' => 'Новичок', 'color' => '#6b7280'];
}

// Функция для получения общего уровня
function getOverallLevel($total_score) {
    if ($total_score >= 90) return 'Эксперт';
    if ($total_score >= 70) return 'Продвинутый';
    if ($total_score >= 50) return 'Средний';
    if ($total_score >= 30) return 'Начинающий';
    return 'Низкий уровень. Критическая зона для развития.';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой прогресс - СтартИнсайт</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('tpl/header.php'); ?>
    
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/programs.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php include('tpl/nav.php'); ?>

    <div class="hero-section progress-page">
    <div class="progress-page-container">
        <div class="progress-main-card">
            <h2 class="section-title">Мой прогресс</h2>
            
            <!-- Статистика -->
            <div class="stats-grid-progress mb-5">
                <div class="stat-card-progress">
                    <div class="stat-number-progress"><?php echo !empty($detailed_soft_skills) ? '1' : '0'; ?></div>
                    <div class="stat-label-progress">Soft Skills тестов</div>
                </div>
                <div class="stat-card-progress">
                    <div class="stat-number-progress"><?php echo count($technical_results); ?></div>
                    <div class="stat-label-progress">Технических тестов</div>
                </div>
                <div class="stat-card-progress">
                    <div class="stat-number-progress">
                        <?php 
                        $total_tests = (!empty($detailed_soft_skills) ? 1 : 0) + count($technical_results);
                        echo $total_tests;
                        ?>
                    </div>
                    <div class="stat-label-progress">Всего тестов</div>
                </div>
                <div class="stat-card-progress">
                    <div class="stat-number-progress">
                        <?php 
                        $max_score = !empty($detailed_soft_skills) ? $detailed_soft_skills['total_score'] : 0;
                        echo $max_score;
                        ?>
                    </div>
                    <div class="stat-label-progress">Общий балл</div>
                </div>
            </div>

            <!-- Soft Skills результаты -->
            <div class="progress-section-card">
                <h3 class="section-title-progress">Результаты теста Soft Skills</h3>
                
                <?php if (!empty($detailed_soft_skills)): ?>
                    <div class="skills-grid-progress">
                        <!-- Левая колонка -->
                        <div class="skills-column-progress">
                            <?php
                            $left_skills = [
                                'communication_skills' => ['title' => 'Коммуникативные навыки', 'detail' => 'active_listening', 'detail_title' => 'Активное слушание'],
                                'teamwork' => ['title' => 'Командная работа', 'detail' => 'collaboration', 'detail_title' => 'Коллаборация'],
                                'problem_solving' => ['title' => 'Решение проблем', 'detail' => 'decision_making', 'detail_title' => 'Принятие решений'],
                                'adaptability' => ['title' => 'Адаптивность', 'detail' => 'learning_agility', 'detail_title' => 'Обучаемость']
                            ];
                            
                            foreach($left_skills as $skill => $data): 
                                $score = $detailed_soft_skills[$skill];
                                $detail_score = $detailed_soft_skills[$data['detail']];
                            ?>
                            <div class="skill-item-progress">
                                <h5 class="skill-title-progress"><?php echo $data['title']; ?></h5>
                                <div class="progress-container-progress">
                                    <div class="progress-bar-progress" style="width: <?php echo ($score / 5) * 100; ?>%">
                                        <?php echo $score; ?>/5
                                    </div>
                                </div>
                                <div class="skill-details-progress">
                                    <?php echo $data['detail_title']; ?>: <?php echo $detail_score; ?>/5
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Правая колонка -->
                        <div class="skills-column-progress">
                            <?php
                            $right_skills = [
                                'leadership' => ['title' => 'Лидерство', 'detail' => 'feedback_skills', 'detail_title' => 'Обратная связь'],
                                'time_management' => ['title' => 'Тайм-менеджмент', 'detail' => 'strategic_thinking', 'detail_title' => 'Стратегическое мышление'],
                                'creativity' => ['title' => 'Креативность', 'detail' => 'conflict_resolution', 'detail_title' => 'Решение конфликтов'],
                                'emotional_intelligence' => ['title' => 'Эмоциональный интеллект', 'detail' => 'stress_management', 'detail_title' => 'Управление стрессом']
                            ];
                            
                            foreach($right_skills as $skill => $data): 
                                $score = $detailed_soft_skills[$skill];
                                $detail_score = $detailed_soft_skills[$data['detail']];
                            ?>
                            <div class="skill-item-progress">
                                <h5 class="skill-title-progress"><?php echo $data['title']; ?></h5>
                                <div class="progress-container-progress">
                                    <div class="progress-bar-progress" style="width: <?php echo ($score / 5) * 100; ?>%">
                                        <?php echo $score; ?>/5
                                    </div>
                                </div>
                                <div class="skill-details-progress">
                                    <?php echo $data['detail_title']; ?>: <?php echo $detail_score; ?>/5
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="total-score-progress">
                        <h4>Общий балл: <?php echo $detailed_soft_skills['total_score']; ?>/80</h4>
                        <p class="text-muted mb-3">Тест пройден: <?php echo date('d.m.Y H:i', strtotime($detailed_soft_skills['test_date'])); ?></p>
                        <a href="soft_skills_test.php" class="btn-progress">Пройти тест заново</a>
                    </div>
                    
                <?php else: ?>
                    <div class="empty-state-progress">
                        <p>Вы еще не проходили тест Soft Skills</p>
                        <a href="soft_skills_test.php" class="btn-progress">Пройти тестирование</a>
                    </div>
                <?php endif; ?>
            
                    </div>
                    <!-- Блок технических тестов -->
                     <div class="total-score-progress">
                            <h3 class="section-title-progress">Результаты технических тестов</h3>
                            
                            <?php if (!empty($technical_results)): ?>
                                <div class="row">
                                    <?php foreach($technical_results as $result): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="test-result-card">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="mb-0"><?php echo htmlspecialchars($result['direction_name'] ?? 'Технический тест'); ?></h5>
                                                    <?php if (!empty($result['level'])): ?>
                                                        <span class="level-badge"><?php echo htmlspecialchars($result['level']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($result['score'])): ?>
                                                    <div class="progress mb-2">
                                                        <div class="progress-bar" style="width: <?php echo min(100, ($result['score'] / ($result['max_score'] ?? 100)) * 100); ?>%">
                                                            <?php echo $result['score']; ?>/<?php echo $result['max_score'] ?? 100; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="test-date">
                                                    <?php echo date('d.m.Y H:i', strtotime($result['test_date'])); ?>
                                                </div>
                                                <?php if (!empty($result['recommendations'])): ?>
                                                    <div class="mt-2">
                                                        <small class="text-muted">Рекомендации: <?php echo htmlspecialchars($result['recommendations']); ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <p>Вы еще не проходили технические тесты</p>
                                    <a href="index.php" class="btn btn-primary">Выбрать направление</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="total-score-progress">
                        <h3 class="section-title-progress">Рекомендации по развитию</h3>
                        
                        <?php if (!empty($detailed_soft_skills)): ?>
    <div class="col-md-6">
        <h5>Персональные рекомендации по Soft Skills:</h5>
        <ul class="list-unstyled">
            <?php
            // Анализ сильных и слабых сторон
            $strong_skills = [];
            $weak_skills = [];
            
            $skills_data = [
                'communication_skills' => 'Коммуникативные навыки',
                'active_listening' => 'Активное слушание',
                'teamwork' => 'Командная работа',
                'collaboration' => 'Коллаборация',
                'problem_solving' => 'Решение проблем',
                'decision_making' => 'Принятие решений',
                'adaptability' => 'Адаптивность',
                'learning_agility' => 'Обучаемость',
                'leadership' => 'Лидерство',
                'feedback_skills' => 'Обратная связь',
                'time_management' => 'Тайм-менеджмент',
                'strategic_thinking' => 'Стратегическое мышление',
                'creativity' => 'Креативность',
                'conflict_resolution' => 'Решение конфликтов',
                'emotional_intelligence' => 'Эмоциональный интеллект',
                'stress_management' => 'Управление стрессом'
            ];
            
            foreach ($skills_data as $skill_key => $skill_name) {
                $score = $detailed_soft_skills[$skill_key];
                if ($score >= 4) {
                    $strong_skills[$skill_name] = $score;
                } elseif ($score <= 2) {
                    $weak_skills[$skill_name] = $score;
                }
            }
            
            // Рекомендации на основе профиля
            echo "<li class='mb-3'><strong>Ваш профиль:</strong><br>";
            
            if (!empty($strong_skills)) {
                $top_skills = array_slice($strong_skills, 0, 3);
                echo "Сильные стороны: " . implode(', ', array_keys($top_skills)) . ".<br>";
            }
            
            if (!empty($weak_skills)) {
                $critical_skills = array_slice($weak_skills, 0, 3);
                echo "Зоны роста: " . implode(', ', array_keys($critical_skills)) . ".";
            }
            echo "</li>";
            
            // Специфические рекомендации
            $specific_recommendations = [];
            
            // Рекомендации по коммуникации
            if ($detailed_soft_skills['communication_skills'] <= 3) {
                $specific_recommendations[] = "<strong>Коммуникация:</strong> Практикуйте pitch-презентации (30 сек о себе). Начните с ежедневных коротких высказываний в группах.";
            }
            
            // Рекомендации по командной работе
            if ($detailed_soft_skills['teamwork'] <= 3) {
                $specific_recommendations[] = "<strong>Командная работа:</strong> Возьмите роль координатора в следующем проекте. Начните с распределения простых задач.";
            }
            
            // Рекомендации по решению проблем
            if ($detailed_soft_skills['problem_solving'] <= 3) {
                $specific_recommendations[] = "<strong>Решение проблем:</strong> Применяйте методику '5 Почему' для анализа рабочих задач. Документируйте процесс.";
            }
            
            // Рекомендации по адаптивности
            if ($detailed_soft_skills['adaptability'] <= 3) {
                $specific_recommendations[] = "<strong>Адаптивность:</strong> Каждую неделю пробуйте новый инструмент/подход в работе. Ведите дневник изменений.";
            }
            
            // Рекомендации по лидерству
            if ($detailed_soft_skills['leadership'] <= 3) {
                $specific_recommendations[] = "<strong>Лидерство:</strong> Возьмите менторинг над junior-специалистом. Начните с 1-часовых сессий раз в 2 недели.";
            }
            
            // Рекомендации по тайм-менеджменту
            if ($detailed_soft_skills['time_management'] <= 3) {
                $specific_recommendations[] = "<strong>Тайм-менеджмент:</strong> Внедрите Pomodoro-технику (25/5). Используйте матрицу Эйзенхауэра для приоритизации.";
            }
            
            // Рекомендации по эмоциональному интеллекту
            if ($detailed_soft_skills['emotional_intelligence'] <= 3) {
                $specific_recommendations[] = "<strong>Эмоциональный интеллект:</strong> Ведите дневник эмоций 2 недели. Анализируйте триггеры и реакции.";
            }
            
            // Рекомендации по стресс-менеджменту
            if ($detailed_soft_skills['stress_management'] <= 3) {
                $specific_recommendations[] = "<strong>Управление стрессом:</strong> Внедрите 5-минутные дыхательные практики 3 раза в день. Техника 4-7-8.";
            }
            
            // Если все хорошо
            if (empty($specific_recommendations)) {
                $specific_recommendations[] = "<strong>Отличные результаты!</strong> Сфокусируйтесь на развитии экспертизы в ваших сильных сторонах. Рассмотрите менторинг других.";
            }
            
            // Вывод рекомендаций
            foreach (array_slice($specific_recommendations, 0, 5) as $rec) {
                echo "<li class='mb-2'>$rec</li>";
            }
            
            // Конкретные шаги
            echo "<li class='mb-3 mt-3'><strong>Конкретные шаги на ближайший месяц:</strong><br>";
            
            $monthly_steps = [];
            if (count($weak_skills) > 0) {
                $first_weak_skill = array_key_first($weak_skills);
                $monthly_steps[] = "1. Сфокусироваться на развитии '$first_weak_skill' (15 минут в день)";
            }
            
            if (count($strong_skills) > 0) {
                $first_strong_skill = array_key_first($strong_skills);
                $monthly_steps[] = "2. Углубить экспертизу в '$first_strong_skill' через менторинг";
            }
            
            $monthly_steps[] = "3. Пройти мини-курс по эмоциональному интеллекту";
            $monthly_steps[] = "4. Провести 2 рабочих встречи в роли фасилитатора";
            $monthly_steps[] = "5. Составить карту профессионального развития";
            
            foreach ($monthly_steps as $step) {
                echo "• $step<br>";
            }
            echo "</li>";
            ?>
        </ul>
    </div>
<?php endif; ?> </div>
                                
                                <div class="col-md-6">
    <h5>Общие рекомендации:</h5>
    <ul class="list-unstyled">
        <?php
        // Определяем уровень развития
        $total_score = $detailed_soft_skills['total_score'] ?? 0;
        
        if ($total_score >= 60) {
            echo "<li class='mb-2'><strong>Уровень: Продвинутый</strong></li>";
            echo "<li class='mb-2'>• Фокус на стратегическом развитии и менторинге</li>";
            echo "<li class='mb-2'>• Разработка личного бренда в профессиональной сфере</li>";
            echo "<li class='mb-2'>• Участие в конференциях как спикер</li>";
            echo "<li class='mb-2'>• Создание методологий и best practices</li>";
        } elseif ($total_score >= 40) {
            echo "<li class='mb-2'><strong>Уровень: Средний</strong></li>";
            echo "<li class='mb-2'>• Фокус на углублении ключевых компетенций</li>";
            echo "<li class='mb-2'>• Участие в кросс-функциональных проектах</li>";
            echo "<li class='mb-2'>• Развитие экспертизы в 2-3 смежных областях</li>";
            echo "<li class='mb-2'>• Получение профессиональных сертификаций</li>";
        } else {
            echo "<li class='mb-2'><strong>Уровень: Начинающий</strong></li>";
            echo "<li class='mb-2'>• Фокус на фундаментальных навыках</li>";
            echo "<li class='mb-2'>• Регулярное прохождение базовых тренингов</li>";
            echo "<li class='mb-2'>• Работа с ментором или коучем</li>";
            echo "<li class='mb-2'>• Постепенное увеличение зоны ответственности</li>";
        }
        ?>
        
        <li class="mb-2 mt-3"><strong>Метрики успеха:</strong></li>
        <li class="mb-2">• Повторное тестирование через 3 месяца (+15% к общему баллу)</li>
        <li class="mb-2">• Реализация 2 рабочих проектов с применением новых навыков</li>
        <li class="mb-2">• Получение обратной связи от 3 коллег</li>
        <li class="mb-2">• Участие в минимум 1 профессиональном мероприятии</li>
        
        <li class="mb-2 mt-3"><strong>Ресурсы для развития:</strong></li>
        <li class="mb-2">• Курс 'Эмоциональный интеллект на работе' (Coursera)</li>
        <li class="mb-2">• Книга '7 навыков высокоэффективных людей' (Стивен Кови)</li>
        <li class="mb-2">• Подкаст 'Soft Skills для IT-специалистов'</li>
        <li class="mb-2">• Сообщество 'Product Tribe' для практики</li>
    </ul>
</div>

                    <!-- Блок целей -->
                    <div class="total-score-progress">
                        <h3 class="section-title-progress">Мои цели</h3>
                        <div class="empty-state">
                            <p>Функция постановки целей скоро будет доступна</p>
                            <p class="small text-muted">В ближайшем обновлении вы сможете ставить цели и отслеживать их выполнение</p>
                        </div>
                    </div>
                    </div>
                </div> <!-- Закрытие auth-card -->
            </div> <!-- Закрытие col-12 col-lg-10 col-xl-9 -->
        </div> <!-- Закрытие row justify-content-center -->
    </div> <!-- Закрытие container -->
</div> <!-- Закрытие hero-section -->


<?php include(__DIR__ . '/tpl/footer.php'); ?>










