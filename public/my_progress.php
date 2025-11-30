<?php
session_start();
if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
    header("Location: avtor.php");
    exit();
}

include("dbconnect.php");
$user_id = $_SESSION['id'];

// Получаем данные пользователя
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_data = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user: " . $e->getMessage());
    $user_data = [];
}

// Получаем профиль пользователя
try {
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $profile_data = $stmt->fetch() ?: [];
} catch (PDOException $e) {
    error_log("Error fetching profile: " . $e->getMessage());
    $profile_data = [];
}

// Получаем результаты Soft Skills тестов из новой таблицы
try {
    $stmt = $pdo->prepare("SELECT * FROM soft_skills_detailed_results WHERE user_id = :user_id ORDER BY test_date DESC LIMIT 5");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $softskills_results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching softskills: " . $e->getMessage());
    $softskills_results = [];
}

// Получаем результаты технических тестов
try {
    $stmt = $pdo->prepare("
        SELECT tr.*, d.name as direction_name 
        FROM technical_results tr 
        LEFT JOIN directions d ON tr.direction_id = d.id 
        WHERE tr.user_id = :user_id 
        ORDER BY tr.test_date DESC 
        LIMIT 10
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $technical_results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching technical results: " . $e->getMessage());
    $technical_results = [];
}

// Получаем цели развития
try {
    $stmt = $pdo->prepare("SELECT * FROM development_goals WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $goals = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching goals: " . $e->getMessage());
    $goals = [];
}

// Статистика
$total_tests = count($softskills_results) + count($technical_results);
$avg_score = 0;

if ($total_tests > 0) {
    $total_score = 0;
    $total_max_score = 0;
    
    foreach ($softskills_results as $test) {
        $total_score += $test['overall_score'];
        $total_max_score += 100;
    }
    
    foreach ($technical_results as $test) {
        $total_score += $test['score'];
        $total_max_score += $test['max_score'];
    }
    
    $avg_score = $total_max_score > 0 ? round(($total_score / $total_max_score) * 100, 1) : 0;
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
    <?php include(__DIR__ . '/tpl/header.php'); ?>
    
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/programs.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php include(__DIR__ . '/tpl/nav.php'); ?>

    <div class="hero-section progress-page">
    <div class="progress-page-container">
        <div class="progress-main-card">
            <h2 class="text-center mb-4">Мой прогресс</h2>
            
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

                        <!-- История тестов -->
            <div class="progress-section-card">
                <h3 class="section-title-progress">История тестирования</h3>
                
                <!-- Soft Skills результаты -->
<?php if (!empty($softskills_results)): ?>
    <h4>Soft Skills тесты</h4>
    <?php foreach($softskills_results as $test): ?>
        <div class="test-item">
            <div class="test-header">
                <h5>Soft Skills Assessment</h5>
                <span class="test-date">
                    <?php echo date('d.m.Y H:i', strtotime($test['test_date'])); ?>
                </span>
            </div>
            <div class="test-result">
                <div class="score">
                    <span class="score-value"><?php echo $test['total_score']; ?>/80</span>
                    <span class="score-label">общий балл</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo ($test['total_score'] / 80) * 100; ?>%"></div>
                </div>
            </div>
            <div class="skill-breakdown">
                <div class="skill-score">Коммуникация: <?php echo $test['communication_skills']; ?>/5</div>
                <div class="skill-score">Активное слушание: <?php echo $test['active_listening']; ?>/5</div>
                <div class="skill-score">Командная работа: <?php echo $test['teamwork']; ?>/5</div>
                <div class="skill-score">Коллаборация: <?php echo $test['collaboration']; ?>/5</div>
                <div class="skill-score">Решение проблем: <?php echo $test['problem_solving']; ?>/5</div>
                <div class="skill-score">Принятие решений: <?php echo $test['decision_making']; ?>/5</div>
                <div class="skill-score">Адаптивность: <?php echo $test['adaptability']; ?>/5</div>
                <div class="skill-score">Обучаемость: <?php echo $test['learning_agility']; ?>/5</div>
                <div class="skill-score">Лидерство: <?php echo $test['leadership']; ?>/5</div>
                <div class="skill-score">Обратная связь: <?php echo $test['feedback_skills']; ?>/5</div>
                <div class="skill-score">Тайм-менеджмент: <?php echo $test['time_management']; ?>/5</div>
                <div class="skill-score">Стратегическое мышление: <?php echo $test['strategic_thinking']; ?>/5</div>
                <div class="skill-score">Креативность: <?php echo $test['creativity']; ?>/5</div>
                <div class="skill-score">Решение конфликтов: <?php echo $test['conflict_resolution']; ?>/5</div>
                <div class="skill-score">Эмоциональный интеллект: <?php echo $test['emotional_intelligence']; ?>/5</div>
                <div class="skill-score">Управление стрессом: <?php echo $test['stress_management']; ?>/5</div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

                <!-- Технические тесты -->
                <?php if (!empty($technical_results)): ?>
                    <h4>Технические тесты</h4>
                    <?php foreach($technical_results as $test): ?>
                        <div class="test-item">
                            <div class="test-header">
                                <h5><?php echo htmlspecialchars($test['test_name']); ?> (<?php echo htmlspecialchars($test['direction_name']); ?>)</h5>
                                <span class="test-date">
                                    <?php echo date('d.m.Y H:i', strtotime($test['test_date'])); ?>
                                </span>
                            </div>
                            <div class="test-result">
                                <div class="score">
                                    <span class="score-value"><?php echo $test['score']; ?>/<?php echo $test['max_score']; ?></span>
                                    <span class="score-label">баллов</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $test['max_score'] > 0 ? round(($test['score'] / $test['max_score']) * 100) : 0; ?>%"></div>
                                </div>
                            </div>
                            <div class="test-interpretation">
                                Уровень: <strong><?php echo htmlspecialchars($test['level']); ?></strong> | 
                                Правильных ответов: <?php echo $test['correct_answers']; ?>/<?php echo $test['total_questions']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (empty($softskills_results) && empty($technical_results)): ?>
                    <div class="empty-state-progress">
                        <p>Пока нет результатов тестов</p>
                        <p>Пройдите тесты, чтобы отслеживать ваш прогресс</p>
                        <a href="soft_skills_test.php" class="btn-progress me-2">Soft Skills тест</a>
                        <a href="directions.php" class="btn-progress">Технические тесты</a>
                    </div>
                <?php endif; ?>
            </div>
                    </div>
                    <!-- Блок технических тестов -->
                     <div class="progress-card">
                            <h3 class="section-title">Результаты технических тестов</h3>
                            
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

                        <div class="progress-card">
                        <h3 class="section-title">Рекомендации по развитию</h3>
                        
                        <?php if (!empty($detailed_soft_skills) || !empty($technical_results)): ?>
                            <div class="row">
                                <?php if (!empty($detailed_soft_skills)): ?>
                                    <div class="col-md-6">
                                        <h5>Soft Skills рекомендации:</h5>
                                        <ul class="list-unstyled">
                                            <?php
                                            $recommendations = [];
                                            
                                            if ($detailed_soft_skills['communication_skills'] < 3) {
                                                $recommendations[] = "Развивайте коммуникативные навыки через практику общения";
                                            }
                                            
                                            if ($detailed_soft_skills['teamwork'] < 3) {
                                                $recommendations[] = "Участвуйте в групповых проектах для улучшения командной работы";
                                            }
                                            
                                            if ($detailed_soft_skills['problem_solving'] < 3) {
                                                $recommendations[] = "Решайте логические задачи для развития аналитического мышления";
                                            }
                                            
                                            if ($detailed_soft_skills['leadership'] < 3) {
                                                $recommendations[] = "Берите на себя ответственность в небольших проектах";
                                            }
                                            
                                            if ($detailed_soft_skills['time_management'] < 3) {
                                                $recommendations[] = "Используйте техники тайм-менеджмента (Pomodoro, Eisenhower Matrix)";
                                            }
                                            
                                            if (empty($recommendations)) {
                                                $recommendations[] = "Отличные результаты! Продолжайте развивать все навыки равномерно";
                                            }
                                            
                                            foreach(array_slice($recommendations, 0, 3) as $rec):
                                            ?>
                                                <li class="mb-2"><?php echo $rec; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="col-md-6">
                                    <h5>Общие рекомендации:</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">Проходите регулярное обучение по выбранным направлениям</li>
                                        <li class="mb-2">Повторяйте тестирование каждые 3 месяца для отслеживания прогресса</li>
                                        <li class="mb-2">Сфокусируйтесь на 2-3 ключевых навыках для развития</li>
                                        <li class="mb-2">Участвуйте в профессиональных сообществах и мероприятиях</li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>Пройдите тесты, чтобы получить персональные рекомендации</p>
                                <div class="mt-3">
                                    <a href="soft_skills_test.php" class="btn btn-primary me-2">Soft Skills тест</a>
                                    <a href="index.php" class="btn btn-secondary">Технические тесты</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Блок целей -->
                    <div class="progress-card">
                        <h3 class="section-title">Мои цели</h3>
                        <div class="empty-state">
                            <p>Функция постановки целей скоро будет доступна</p>
                            <p class="small text-muted">В ближайшем обновлении вы сможете ставить цели и отслеживать их выполнение</p>
                        </div>
                    </div>
                    
                </div> <!-- Закрытие auth-card -->
            </div> <!-- Закрытие col-12 col-lg-10 col-xl-9 -->
        </div> <!-- Закрытие row justify-content-center -->
    </div> <!-- Закрытие container -->
</div> <!-- Закрытие hero-section -->


<?php include(__DIR__ . '/tpl/footer.php'); ?>




