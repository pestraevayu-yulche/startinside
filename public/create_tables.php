<?php
include('dbconnect.php');

try {
    // Таблица профилей
    $pdo->exec("
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
        )
    ");
    echo "✅ Таблица user_profiles создана!<br>";

    // Добавляем 6 направлений
    $pdo->exec("DELETE FROM directions"); // Очищаем старые
    
    $directions = [
        ['Backend-разработчик', 'Специалист по серверной части приложений', 'Java/Python/C#, SQL/NoSQL, Spring/Django, Docker, Kubernetes, REST API, микросервисы', 'Junior Backend → Middle Backend → Senior Backend → Tech Lead → Architect', 'backend'],
        ['Frontend-разработчик', 'Специалист по клиентской части приложений', 'JavaScript/TypeScript, React/Vue/Angular, HTML/CSS, Webpack, Responsive Design, State Management', 'Junior Frontend → Middle Frontend → Senior Frontend → Team Lead → Frontend Architect', 'frontend'],
        ['Data Scientist', 'Специалист по анализу данных и машинному обучению', 'Python/R, SQL, Pandas/NumPy, Machine Learning, Statistics, Data Visualization, Big Data tools', 'Junior Data Scientist → Middle Data Scientist → Senior Data Scientist → ML Engineer → Chief Data Officer', 'data'],
        ['DevOps-инженер', 'Специалист по автоматизации процессов разработки', 'Docker, Kubernetes, CI/CD, AWS/GCP, Terraform, Ansible, Linux, Bash, Monitoring', 'Junior DevOps → Middle DevOps → Senior DevOps → DevOps Lead → SRE', 'devops'],
        ['Mobile-разработчик', 'Специалист по созданию мобильных приложений', 'Kotlin/Swift, React Native/Flutter, Android SDK/iOS SDK, REST API, Mobile UI/UX', 'Junior Mobile → Middle Mobile → Senior Mobile → Mobile Team Lead → Mobile Architect', 'mobile'],
        ['QA-инженер', 'Специалист по обеспечению качества ПО', 'Manual Testing, Automated Testing, Selenium/Cypress, Test Cases, Bug Tracking, SQL, API Testing', 'Junior QA → Middle QA → Senior QA → QA Lead → QA Manager', 'qa']
    ];

    $stmt = $pdo->prepare("INSERT INTO directions (name, description, skills, career_paths, icon) VALUES (?, ?, ?, ?, ?)");
    foreach ($directions as $direction) {
        $stmt->execute($direction);
    }
    echo "✅ 6 направлений добавлено!<br>";

} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>
