<?php
include('dbconnect.php');

echo "<h1>Исправление структуры направлений</h1>";

$sql = "
-- Удаляем старую таблицу если есть
DROP TABLE IF EXISTS directions;

-- Создаем таблицу направлений с правильной структурой
CREATE TABLE directions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    skills TEXT NOT NULL,
    career_paths TEXT NOT NULL,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставляем данные по IT профессиям
INSERT INTO directions (name, description, skills, career_paths, icon) VALUES
(
    'Backend-разработчик',
    'Специалист по серверной части приложений. Отвечает за бизнес-логику, базы данных, API и производительность системы.',
    'Java/Python/C#, SQL/NoSQL, Spring/Django, Docker, Kubernetes, REST API, микросервисы',
    'Junior Backend → Middle Backend → Senior Backend → Tech Lead → Architect',
    'backend'
),
(
    'Frontend-разработчик', 
    'Специалист по клиентской части приложений. Создает интерфейсы, с которыми взаимодействуют пользователи.',
    'JavaScript/TypeScript, React/Vue/Angular, HTML/CSS, Webpack, Responsive Design, State Management',
    'Junior Frontend → Middle Frontend → Senior Frontend → Team Lead → Frontend Architect',
    'frontend'
),
(
    'Data Scientist',
    'Специалист по анализу данных и машинному обучению. Извлекает insights из данных и строит predictive модели.',
    'Python/R, SQL, Pandas/NumPy, Machine Learning, Statistics, Data Visualization, Big Data tools',
    'Junior DS → Middle DS → Senior DS → ML Engineer → Chief Data Officer',
    'data'
),
(
    'DevOps-инженер',
    'Специалист по автоматизации процессов разработки и развертывания приложений.',
    'Docker, Kubernetes, CI/CD, AWS/GCP, Terraform, Ansible, Linux, Monitoring',
    'Junior DevOps → Middle DevOps → Senior DevOps → DevOps Lead → SRE',
    'devops'
),
(
    'Mobile-разработчик',
    'Специалист по созданию мобильных приложений для iOS и Android.',
    'Swift/Kotlin, React Native/Flutter, REST API, Mobile UI/UX, App Store/Google Play',
    'Junior Mobile → Middle Mobile → Senior Mobile → Mobile Team Lead',
    'mobile'
);
";

try {
    $pdo->exec($sql);
    echo "✅ Структура направлений исправлена!<br>";
    
    // Проверяем
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM directions");
    $result = $stmt->fetch();
    echo "✅ Направлений создано: " . $result['count'] . "<br>";
    
    // Показываем направления
    $stmt = $pdo->query("SELECT name FROM directions");
    $directions = $stmt->fetchAll();
    
    echo "<h2>Созданные направления:</h2>";
    foreach ($directions as $dir) {
        echo "✅ " . $dir['name'] . "<br>";
    }
    
    echo "<p style='color: green; font-weight: bold;'>🎉 Направления готовы к работе!</p>";
    
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>
