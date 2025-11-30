<?php
include('tpl/header.php');
?>
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/log.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php
    include('tpl/nav.php');
    ?>

    <!-- Основной контент -->
    <div class="hero-section">
        <h1 class="main-title">СтартИнсайт</h1>
        <p class="subtitle">Определите ваш реальный уровень в IT, выявите сильные стороны и профессиональные дефициты, постройте персональный карьерный путь</p>
        
        <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
            <a href="#analysis-process" class="cta-button">Начать анализ</a>
        <?php else: ?>
            <a href="registr.php" class="cta-button">Начать анализ</a>
        <?php endif; ?>
        
        <div class="features">
            <div class="feature-card">
                <h3 class="feature-title">Точная диагностика</h3>
                <p class="feature-text">Комплексная оценка hard и soft skills для определения реального уровня разработчика</p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Выявление дефицитов</h3>
                <p class="feature-text">Определяем пробелы в знаниях, мешающие карьерному росту и повышению грейда</p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Персональный план</h3>
                <p class="feature-text">Строим индивидуальную траекторию развития с конкретными шагами и сроками</p>
            </div>
        </div>

<!-- Процесс анализа -->
<div class="analysis-process mt-5" id="analysis-process">
            <h2 class="process-title">4 шага к построению успешной IT-карьеры</h2>
            
            <div class="process-steps">
                <!-- Шаг 1 -->
                <div class="process-step">
                    <div class="step-header">
                        <h3>Soft Skills оценка</h3>
                    </div>
                    <div class="step-content">
                        <p>Определите ваши сильные стороны и зоны роста в гибких навыках: коммуникация, работа в команде, лидерство, адаптивность</p>
                        <div class="step-actions">
                            <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
                                <a href="soft_skills_test.php" class="btn btn-primary step-btn">
                                    Пройти тест
                                </a>
                            <?php else: ?>
                                <a href="avtor.php" class="btn btn-primary step-btn">
                                    Войти для прохождения
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Шаг 2 -->
                <div class="process-step">
                    <div class="step-header">
                        <h3>Технический уровень</h3>
                    </div>
                    <div class="step-content">
                        <p>Проверьте соответствие уровню Junior/Middle/Senior по ключевым технологическим компетенциям и определите реальный грейд</p>
                        <div class="step-actions">
                            <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
                                <a href="directions.php" class="btn btn-primary step-btn">
                                    Выбрать направление
                                </a>
                            <?php else: ?>
                                <a href="avtor.php" class="btn btn-primary step-btn">
                                    Войти для прохождения
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Шаг 3 -->
                <div class="process-step">
                    <div class="step-header">
                        <h3>Анализ дефицитов</h3>
                    </div>
                    <div class="step-content">
                        <p>Получите детальный отчет с выявленными пробелами в знаниях и навыках, которые мешают карьерному росту</p>
                        <div class="step-actions">
                            <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
                                <a href="my_progress.php" class="btn btn-primary step-btn">
                                    Посмотреть прогресс
                                </a>
                            <?php else: ?>
                                <a href="avtor.php" class="btn btn-primary step-btn">
                                    Войти для просмотра
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Шаг 4 -->
                <div class="process-step">
                    <div class="step-header">
                        <h3>Карьерный план</h3>
                    </div>
                    <div class="step-content">
                        <p>Постройте персональную карьерную траекторию с конкретными шагами развития, сроками и рекомендациями</p>
                        <div class="step-actions">
                            <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])): ?>
                                <a href="career_plan.php" class="btn btn-primary step-btn">
                                    Построить план
                                </a>
                            <?php else: ?>
                                <a href="avtor.php" class="btn btn-primary step-btn">
                                    Войти для построения
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>


<?php
include('tpl/footer.php');

?>

