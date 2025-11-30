<?php
session_start();
if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
    header("Location: avtor.php");
    exit();
}

include("dbconnect.php");

// Сохранение результатов расширенного теста
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_test'])) {
    $user_id = $_SESSION['id'];
    
    // Основные навыки
    $communication_skills = intval($_POST['communication_skills'] ?? 0);
    $teamwork = intval($_POST['teamwork'] ?? 0);
    $problem_solving = intval($_POST['problem_solving'] ?? 0);
    $adaptability = intval($_POST['adaptability'] ?? 0);
    $leadership = intval($_POST['leadership'] ?? 0);
    $time_management = intval($_POST['time_management'] ?? 0);
    $creativity = intval($_POST['creativity'] ?? 0);
    $emotional_intelligence = intval($_POST['emotional_intelligence'] ?? 0);
    
    // Детальные суб-навыки
    $active_listening = intval($_POST['active_listening'] ?? 0);
    $conflict_resolution = intval($_POST['conflict_resolution'] ?? 0);
    $feedback_skills = intval($_POST['feedback_skills'] ?? 0);
    $collaboration = intval($_POST['collaboration'] ?? 0);
    $decision_making = intval($_POST['decision_making'] ?? 0);
    $stress_management = intval($_POST['stress_management'] ?? 0);
    $learning_agility = intval($_POST['learning_agility'] ?? 0);
    $strategic_thinking = intval($_POST['strategic_thinking'] ?? 0);
    
    // Общий балл (максимум 120)
    $total_score = $communication_skills + $teamwork + $problem_solving + $adaptability + 
                   $leadership + $time_management + $creativity + $emotional_intelligence +
                   $active_listening + $conflict_resolution + $feedback_skills + $collaboration +
                   $decision_making + $stress_management + $learning_agility + $strategic_thinking;
    
    try {
        // Проверяем, есть ли уже результат
        $check_stmt = $pdo->prepare("SELECT id FROM soft_skills_detailed_results WHERE user_id = :user_id");
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $query = "UPDATE soft_skills_detailed_results SET 
                     communication_skills = :communication_skills,
                     teamwork = :teamwork,
                     problem_solving = :problem_solving,
                     adaptability = :adaptability,
                     leadership = :leadership,
                     time_management = :time_management,
                     creativity = :creativity,
                     emotional_intelligence = :emotional_intelligence,
                     active_listening = :active_listening,
                     conflict_resolution = :conflict_resolution,
                     feedback_skills = :feedback_skills,
                     collaboration = :collaboration,
                     decision_making = :decision_making,
                     stress_management = :stress_management,
                     learning_agility = :learning_agility,
                     strategic_thinking = :strategic_thinking,
                     total_score = :total_score,
                     test_date = NOW()
                     WHERE user_id = :user_id";
        } else {
            $query = "INSERT INTO soft_skills_detailed_results 
                     (user_id, communication_skills, teamwork, problem_solving, adaptability, 
                      leadership, time_management, creativity, emotional_intelligence,
                      active_listening, conflict_resolution, feedback_skills, collaboration,
                      decision_making, stress_management, learning_agility, strategic_thinking, total_score) 
                     VALUES 
                     (:user_id, :communication_skills, :teamwork, :problem_solving, :adaptability,
                      :leadership, :time_management, :creativity, :emotional_intelligence,
                      :active_listening, :conflict_resolution, :feedback_skills, :collaboration,
                      :decision_making, :stress_management, :learning_agility, :strategic_thinking, :total_score)";
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        // Основные навыки
        $stmt->bindParam(':communication_skills', $communication_skills);
        $stmt->bindParam(':teamwork', $teamwork);
        $stmt->bindParam(':problem_solving', $problem_solving);
        $stmt->bindParam(':adaptability', $adaptability);
        $stmt->bindParam(':leadership', $leadership);
        $stmt->bindParam(':time_management', $time_management);
        $stmt->bindParam(':creativity', $creativity);
        $stmt->bindParam(':emotional_intelligence', $emotional_intelligence);
        // Суб-навыки
        $stmt->bindParam(':active_listening', $active_listening);
        $stmt->bindParam(':conflict_resolution', $conflict_resolution);
        $stmt->bindParam(':feedback_skills', $feedback_skills);
        $stmt->bindParam(':collaboration', $collaboration);
        $stmt->bindParam(':decision_making', $decision_making);
        $stmt->bindParam(':stress_management', $stress_management);
        $stmt->bindParam(':learning_agility', $learning_agility);
        $stmt->bindParam(':strategic_thinking', $strategic_thinking);
        $stmt->bindParam(':total_score', $total_score);
        
        if ($stmt->execute()) {
            $_SESSION['test_success'] = 'Результаты углубленного теста успешно сохранены!';
            header("Location: my_progress.php");
            exit();
        } else {
            $_SESSION['test_error'] = 'Ошибка при сохранении результатов';
        }
        
    } catch (PDOException $e) {
        error_log("Detailed soft skills test save error: " . $e->getMessage());
        $_SESSION['test_error'] = 'Ошибка при сохранении результатов: ' . $e->getMessage();
    }
}

// Получаем данные пользователя
$user_id = $_SESSION['id'];
$user_data = array();
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_data = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Углубленный тест Soft Skills - СтартИнсайт</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include(__DIR__ . '/tpl/header.php'); ?>
    
    <div class="background-container">
        <img src="img/programs.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php include(__DIR__ . '/tpl/nav.php'); ?>

    <div class="hero-section">
        <div class="test-container">
            <div class="test-header">
                <h1 class="test-title">Углубленный тест Soft Skills</h1>
                <p class="test-subtitle">Оцените свои гибкие навыки по 16 ключевым параметрам для профессионального роста</p>
            </div>
            
            <?php if (isset($_SESSION['test_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['test_error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['test_error']); ?>
            <?php endif; ?>
            
            <div class="test-progress">
                <div class="progress-info">
                    <h5 class="text-white mb-0">Прогресс тестирования</h5>
                    <span class="text-white" id="progressText">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="category-completion">
                    Заполнено: <span id="completedCategories">0</span> из 8 категорий
                </div>
            </div>
            
            <form id="detailedSoftSkillsForm" method="post">
                <div class="skills-grid">
                    
                    <!-- Коммуникативные навыки -->
                    <div class="skill-category" data-category="communication">
                        <div class="category-header">
                            <div class="category-icon">1</div>
                            <h3 class="category-title">Коммуникация</h3>
                        </div>
                        <p class="category-description">Эффективное общение, умение слушать и ясно выражать мысли</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею четко и структурированно доносить свои мысли</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="communication_skills" value="<?php echo $i; ?>" id="comm_<?php echo $i; ?>" onchange="updateCategoryProgress('communication')">
                                        <label for="comm_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="rating-labels">
                                <span>Совсем нет</span>
                                <span>Отлично</span>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я внимательно слушаю собеседника и задаю уточняющие вопросы</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="active_listening" value="<?php echo $i; ?>" id="listen_<?php echo $i; ?>" onchange="updateCategoryProgress('communication')">
                                        <label for="listen_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Командная работа -->
                    <div class="skill-category" data-category="teamwork">
                        <div class="category-header">
                            <div class="category-icon">2</div>
                            <h3 class="category-title">Командная работа</h3>
                        </div>
                        <p class="category-description">Эффективное взаимодействие в команде и совместное достижение целей</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я легко нахожу общий язык с коллегами и готов к компромиссам</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="teamwork" value="<?php echo $i; ?>" id="team_<?php echo $i; ?>" onchange="updateCategoryProgress('teamwork')">
                                        <label for="team_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я активно участвую в совместных проектах и помогаю коллегам</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="collaboration" value="<?php echo $i; ?>" id="collab_<?php echo $i; ?>" onchange="updateCategoryProgress('teamwork')">
                                        <label for="collab_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Решение проблем -->
                    <div class="skill-category" data-category="problems">
                        <div class="category-header">
                            <div class="category-icon">3</div>
                            <h3 class="category-title">Решение проблем</h3>
                        </div>
                        <p class="category-description">Анализ сложных ситуаций и поиск эффективных решений</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею анализировать проблемы и находить системные решения</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="problem_solving" value="<?php echo $i; ?>" id="prob_<?php echo $i; ?>" onchange="updateCategoryProgress('problems')">
                                        <label for="prob_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я принимаю взвешенные решения на основе анализа информации</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="decision_making" value="<?php echo $i; ?>" id="decide_<?php echo $i; ?>" onchange="updateCategoryProgress('problems')">
                                        <label for="decide_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Адаптивность -->
                    <div class="skill-category" data-category="adaptability">
                        <div class="category-header">
                            <div class="category-icon">4</div>
                            <h3 class="category-title">Адаптивность</h3>
                        </div>
                        <p class="category-description">Гибкость в меняющихся условиях и быстрая обучаемость</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я легко адаптируюсь к изменениям и новым условиям работы</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="adaptability" value="<?php echo $i; ?>" id="adapt_<?php echo $i; ?>" onchange="updateCategoryProgress('adaptability')">
                                        <label for="adapt_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я быстро осваиваю новые инструменты и технологии</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="learning_agility" value="<?php echo $i; ?>" id="learn_<?php echo $i; ?>" onchange="updateCategoryProgress('adaptability')">
                                        <label for="learn_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Лидерство -->
                    <div class="skill-category" data-category="leadership">
                        <div class="category-header">
                            <div class="category-icon">5</div>
                            <h3 class="category-title">Лидерство</h3>
                        </div>
                        <p class="category-description">Способность вдохновлять, мотивировать и направлять команду</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я могу взять на себя ответственность и повести команду за собой</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="leadership" value="<?php echo $i; ?>" id="lead_<?php echo $i; ?>" onchange="updateCategoryProgress('leadership')">
                                        <label for="lead_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею конструктивно давать обратную связь коллегам</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="feedback_skills" value="<?php echo $i; ?>" id="feedback_<?php echo $i; ?>" onchange="updateCategoryProgress('leadership')">
                                        <label for="feedback_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Тайм-менеджмент -->
                    <div class="skill-category" data-category="time">
                        <div class="category-header">
                            <div class="category-icon">6</div>
                            <h3 class="category-title">Тайм-менеджмент</h3>
                        </div>
                        <p class="category-description">Эффективное планирование времени и соблюдение дедлайнов</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею эффективно планировать свое время и задачи</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="time_management" value="<?php echo $i; ?>" id="time_<?php echo $i; ?>" onchange="updateCategoryProgress('time')">
                                        <label for="time_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я соблюдаю дедлайны и умею расставлять приоритеты</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="strategic_thinking" value="<?php echo $i; ?>" id="strategy_<?php echo $i; ?>" onchange="updateCategoryProgress('time')">
                                        <label for="strategy_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Креативность -->
                    <div class="skill-category" data-category="creativity">
                        <div class="category-header">
                            <div class="category-icon">7</div>
                            <h3 class="category-title">Креативность</h3>
                        </div>
                        <p class="category-description">Генерация новых идей и нестандартных подходов к решению задач</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я часто придумываю оригинальные идеи и решения</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="creativity" value="<?php echo $i; ?>" id="creat_<?php echo $i; ?>" onchange="updateCategoryProgress('creativity')">
                                        <label for="creat_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею находить нестандартные подходы к решению задач</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="conflict_resolution" value="<?php echo $i; ?>" id="conflict_<?php echo $i; ?>" onchange="updateCategoryProgress('creativity')">
                                        <label for="conflict_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Эмоциональный интеллект -->
                    <div class="skill-category" data-category="emotional">
                        <div class="category-header">
                            <div class="category-icon">8</div>
                            <h3 class="category-title">Эмоциональный интеллект</h3>
                        </div>
                        <p class="category-description">Понимание и управление эмоциями в рабочих ситуациях</p>
                        
                        <div class="question-item">
                            <div class="question-text">Я хорошо понимаю свои эмоции и могу управлять ими в стрессе</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="emotional_intelligence" value="<?php echo $i; ?>" id="emo_<?php echo $i; ?>" onchange="updateCategoryProgress('emotional')">
                                        <label for="emo_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="question-item">
                            <div class="question-text">Я умею сохранять спокойствие и продуктивность в стрессовых ситуациях</div>
                            <div class="rating-scale">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" name="stress_management" value="<?php echo $i; ?>" id="stress_<?php echo $i; ?>" onchange="updateCategoryProgress('emotional')">
                                        <label for="stress_<?php echo $i; ?>"><?php echo $i; ?></label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="test-actions">
                    <button type="submit" name="submit_test" class="submit-btn">Сохранить результаты теста</button>
                    <a href="my_progress.php" class="back-btn">Вернуться к прогрессу</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const categories = ['communication', 'teamwork', 'problems', 'adaptability', 'leadership', 'time', 'creativity', 'emotional'];
        
        function updateCategoryProgress(category) {
            const categoryElement = document.querySelector(`[data-category="${category}"]`);
            const inputs = categoryElement.querySelectorAll('input[type="radio"]:checked');
            const categoryProgress = (inputs.length / 2) * 100;
            
            // Обновляем общий прогресс
            updateOverallProgress();
        }
        
        function updateOverallProgress() {
            let totalQuestions = 0;
            let answeredQuestions = 0;
            
            categories.forEach(category => {
                const categoryElement = document.querySelector(`[data-category="${category}"]`);
                const inputs = categoryElement.querySelectorAll('input[type="radio"]');
                const checkedInputs = categoryElement.querySelectorAll('input[type="radio"]:checked');
                
                totalQuestions += inputs.length;
                answeredQuestions += checkedInputs.length;
            });
            
            const progress = (answeredQuestions / totalQuestions) * 100;
            const completedCategories = categories.filter(category => {
                const categoryElement = document.querySelector(`[data-category="${category}"]`);
                const checkedInputs = categoryElement.querySelectorAll('input[type="radio"]:checked');
                return checkedInputs.length === 2;
            }).length;
            
            document.getElementById('progressFill').style.width = progress + '%';
            document.getElementById('progressText').textContent = Math.round(progress) + '%';
            document.getElementById('completedCategories').textContent = completedCategories;
        }
        
        // Инициализация прогресса
        document.addEventListener('DOMContentLoaded', function() {
            updateOverallProgress();
        });
    </script>

    <?php include(__DIR__ . '/tpl/footer.php'); ?>
</body>

</html>
