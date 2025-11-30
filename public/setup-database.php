<?php
include("dbconnect.php");

$sql = "
-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица профилей пользователей
CREATE TABLE IF NOT EXISTS user_profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    surname VARCHAR(100),
    patronymic VARCHAR(100),
    birth_date DATE,
    work_place TEXT,
    education VARCHAR(50),
    phone VARCHAR(20),
    city VARCHAR(100),
    skills TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица направлений (IT-специальности)
CREATE TABLE IF NOT EXISTS directions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    skills TEXT,
    career_paths TEXT
);

-- Таблица результатов soft skills тестов
CREATE TABLE IF NOT EXISTS soft_skills_detailed_results (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    communication_skills INTEGER CHECK (communication_skills BETWEEN 1 AND 5),
    teamwork INTEGER CHECK (teamwork BETWEEN 1 AND 5),
    problem_solving INTEGER CHECK (problem_solving BETWEEN 1 AND 5),
    adaptability INTEGER CHECK (adaptability BETWEEN 1 AND 5),
    leadership INTEGER CHECK (leadership BETWEEN 1 AND 5),
    time_management INTEGER CHECK (time_management BETWEEN 1 AND 5),
    creativity INTEGER CHECK (creativity BETWEEN 1 AND 5),
    emotional_intelligence INTEGER CHECK (emotional_intelligence BETWEEN 1 AND 5),
    active_listening INTEGER CHECK (active_listening BETWEEN 1 AND 5),
    conflict_resolution INTEGER CHECK (conflict_resolution BETWEEN 1 AND 5),
    feedback_skills INTEGER CHECK (feedback_skills BETWEEN 1 AND 5),
    collaboration INTEGER CHECK (collaboration BETWEEN 1 AND 5),
    decision_making INTEGER CHECK (decision_making BETWEEN 1 AND 5),
    stress_management INTEGER CHECK (stress_management BETWEEN 1 AND 5),
    learning_agility INTEGER CHECK (learning_agility BETWEEN 1 AND 5),
    strategic_thinking INTEGER CHECK (strategic_thinking BETWEEN 1 AND 5),
    total_score INTEGER,
    test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица технических тестов
CREATE TABLE IF NOT EXISTS test_results (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    direction_id INTEGER REFERENCES directions(id),
    score INTEGER,
    max_score INTEGER DEFAULT 100,
    level VARCHAR(50),
    recommendations TEXT,
    test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Добавьте тестовые направления
INSERT INTO directions (name, description, skills, career_paths) VALUES
('Frontend-разработка', 'Разработка пользовательского интерфейса и взаимодействия с пользователем', 'HTML, CSS, JavaScript, React, Vue.js, TypeScript', 'Junior Frontend Developer, Middle Frontend Developer, Senior Frontend Developer, Team Lead'),
('Backend-разработка', 'Разработка серверной части веб-приложений и API', 'PHP, Python, Node.js, Java, C#, SQL, PostgreSQL, MySQL', 'Junior Backend Developer, Middle Backend Developer, Senior Backend Developer, Architect'),
('Fullstack-разработка', 'Разработка как клиентской, так и серверной части приложений', 'HTML, CSS, JavaScript, PHP/Python/Java, SQL, фреймворки', 'Fullstack Developer, Tech Lead, Project Manager'),
('Data Science', 'Анализ данных, машинное обучение и искусственный интеллект', 'Python, R, SQL, статистика, машинное обучение, pandas, numpy', 'Data Analyst, Data Scientist, ML Engineer, AI Specialist')
ON CONFLICT (name) DO NOTHING;
";

try {
    $pdo->exec($sql);
    echo "Таблицы успешно созданы!";
    
    // Проверяем создание
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM directions");
    $result = $stmt->fetch();
    echo "<br>Направлений в базе: " . $result['count'];
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>