<?php 
session_start();
if (empty($_SESSION['login']) or empty($_SESSION['id'])) {
    header("Location: avtor.php");
    exit();
}

// Подключаем базу данных
include("dbconnect.php");

// Обработка сохранения данных профиля
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $user_id = $_SESSION['id'];
    
    // Безопасное получение данных с валидацией
    $surname = trim($_POST['surname'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $work_place = trim($_POST['work_place'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    
    // Валидация даты рождения - преобразуем пустую строку в NULL
    if (empty($birth_date)) {
        $birth_date = null;
    } else {
        // Проверяем корректность формата даты
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
            $_SESSION['error_message'] = 'Неверный формат даты рождения';
            header("Location: profile.php");
            exit();
        }
    }
    
    // Обработка загрузки фото (остается без изменений)
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = 'uploads/avatars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
        $photo_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            try {
                $stmt = $pdo->prepare("SELECT photo FROM user_profiles WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $old_photo_data = $stmt->fetch();
                
                if ($old_photo_data && !empty($old_photo_data['photo']) && file_exists($old_photo_data['photo'])) {
                    unlink($old_photo_data['photo']);
                }
            } catch (PDOException $e) {
                error_log("Error checking old photo: " . $e->getMessage());
            }
        } else {
            $photo_path = '';
        }
    }
    
    try {
        // Проверяем, существует ли уже профиль
        $check_stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->execute();
        $profile_exists = $check_stmt->rowCount() > 0;
        
        if ($profile_exists) {
            // Обновляем существующий профиль
            if ($photo_path) {
                $query = "UPDATE user_profiles SET surname = :surname, patronymic = :patronymic, 
                         birth_date = :birth_date, work_place = :work_place, education = :education, 
                         phone = :phone, city = :city, skills = :skills, photo = :photo_path, 
                         updated_at = NOW() WHERE user_id = :user_id";
            } else {
                $query = "UPDATE user_profiles SET surname = :surname, patronymic = :patronymic, 
                         birth_date = :birth_date, work_place = :work_place, education = :education, 
                         phone = :phone, city = :city, skills = :skills, updated_at = NOW() 
                         WHERE user_id = :user_id";
            }
        } else {
            // Создаем новый профиль
            $query = "INSERT INTO user_profiles (user_id, surname, patronymic, birth_date, work_place, 
                     education, phone, city, skills, photo, created_at, updated_at) 
                     VALUES (:user_id, :surname, :patronymic, :birth_date, :work_place, 
                     :education, :phone, :city, :skills, :photo_path, NOW(), NOW())";
        }
        
        // Подготавливаем и выполняем запрос
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':patronymic', $patronymic);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':work_place', $work_place);
        $stmt->bindParam(':education', $education);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':skills', $skills);
        
        if ($photo_path) {
            $stmt->bindParam(':photo_path', $photo_path);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Профиль успешно сохранен!';
        } else {
            $_SESSION['error_message'] = 'Ошибка при сохранении профиля';
        }
        
    } catch (PDOException $e) {
        error_log("Profile save error: " . $e->getMessage());
        $_SESSION['error_message'] = 'Ошибка при сохранении профиля: ' . $e->getMessage();
    }
    
    header("Location: profile.php");
    exit();
}

// Получаем данные профиля
$user_id = $_SESSION['id'];
$profile_data = array();

try {
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $profile_data = $stmt->fetch();
    }
} catch (PDOException $e) {
    error_log("Error fetching profile: " . $e->getMessage());
}

// Получаем основную информацию пользователя
$user_data = array();
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_data = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

// Получаем имя пользователя для аватара
$user_name = $user_data['name'] ?? ($_SESSION['login'] ?? 'U');
$first_letter = strtoupper(mb_substr($user_name, 0, 1));
?>

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

<div class="hero-section">
    <div class="auth-container profile-container">
        <div class="profile-card">        
                
                <!-- Заголовок -->
                <h2 class="profile-title">Мой профиль</h2>
        
                
                <!-- Сообщения об успехе/ошибке -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                
                <form action="profile.php" method="post" enctype="multipart/form-data" id="profileForm">
    <div class="row">
        <!-- Левая колонка - фото и основная информация -->
        <div class="col-lg-4 col-md-5"> <!-- Увеличиваем ширину левой колонки -->
            <!-- Секция загрузки фото -->
            <div class="profile-photo-section">
                <div class="profile-avatar">
                    <div class="avatar-circle" id="avatarPreview" 
                         style="<?php echo !empty($profile_data['photo']) ? 'background-image: url(' . $profile_data['photo'] . ');' : '' ?>">
                        <?php if (empty($profile_data['photo'])): ?>
                            <?php echo $first_letter; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="photo-upload">
                    <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                    <label for="photoInput" class="btn btn-outline-primary">
                        <i class="fas fa-camera"></i> 
                        <?php echo empty($profile_data['photo']) ? 'Добавить фото' : 'Изменить фото'; ?>
                    </label>
                    <?php if (!empty($profile_data['photo'])): ?>
                        <button type="button" class="btn btn-outline-danger" onclick="removePhoto()">
                            <i class="fas fa-trash"></i> Удалить фото
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Основная информация -->
            <div class="basic-info-card">
                <h5 class="basic-info-title">Основная информация</h5>
                <div class="contact-list">
                    <div class="contact-item">
                        <span class="contact-label">Имя:</span>
                        <span class="contact-value"><?php echo htmlspecialchars($user_data['name'] ?? ''); ?></span>
                    </div>
                    <div class="contact-item">
                        <span class="contact-label">Логин:</span>
                        <span class="contact-value"><?php echo htmlspecialchars($user_data['login'] ?? ''); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Правая колонка - форма редактирования -->
        <div class="col-lg-8 col-md-7"> <!-- Увеличиваем ширину правой колонки -->
            <div class="profile-form-section">
                <h4 class="form-section-title">Персональные данные</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Фамилия</label>
                        <input type="text" name="surname" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($profile_data['surname'] ?? ''); ?>"
                               placeholder="Введите фамилию">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Отчество</label>
                        <input type="text" name="patronymic" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($profile_data['patronymic'] ?? ''); ?>"
                               placeholder="Введите отчество">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Дата рождения</label>
                        <input type="date" name="birth_date" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($profile_data['birth_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="tel" name="phone" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($profile_data['phone'] ?? ''); ?>"
                               placeholder="+7 (XXX) XXX-XX-XX">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Город</label>
                    <input type="text" name="city" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($profile_data['city'] ?? ''); ?>"
                           placeholder="Введите ваш город">
                </div>
                
                <h4 class="form-section-title mt-5">Профессиональная информация</h4>
                
                <div class="mb-3">
                    <label class="form-label">Место работы/учебы</label>
                    <input type="text" name="work_place" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($profile_data['work_place'] ?? ''); ?>"
                           placeholder="Название компании или учебного заведения">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Образование</label>
                    <select name="education" class="form-select form-select-lg">
                        <option value="">Выберите образование</option>
                        <option value="school" <?php echo ($profile_data['education'] ?? '') == 'school' ? 'selected' : ''; ?>>Среднее</option>
                        <option value="college" <?php echo ($profile_data['education'] ?? '') == 'college' ? 'selected' : ''; ?>>Среднее специальное</option>
                        <option value="bachelor" <?php echo ($profile_data['education'] ?? '') == 'bachelor' ? 'selected' : ''; ?>>Бакалавр</option>
                        <option value="master" <?php echo ($profile_data['education'] ?? '') == 'master' ? 'selected' : ''; ?>>Магистр</option>
                        <option value="phd" <?php echo ($profile_data['education'] ?? '') == 'phd' ? 'selected' : ''; ?>>Аспирантура</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Навыки и умения</label>
                    <textarea name="skills" class="form-control form-control-lg" rows="5" 
                              placeholder="Опишите ваши профессиональные навыки, хобби, интересы..."><?php echo htmlspecialchars($profile_data['skills'] ?? ''); ?></textarea>
                </div>
                
                <div class="profile-actions mt-5">
                    <button type="submit" name="save_profile" class="submit-btn">
                        <i class="fas fa-save"></i>
                        <?php echo empty($profile_data) ? 'Сохранить профиль' : 'Обновить профиль'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
            </div>
        </div>
    </div>

    <script>
        // Предпросмотр фото
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').style.backgroundImage = `url(${e.target.result})`;
                    document.getElementById('avatarPreview').innerHTML = '';
                }
                reader.readAsDataURL(file);
            }
        });
        
        function removePhoto() {
            if (confirm('Вы уверены, что хотите удалить фото?')) {
                document.getElementById('avatarPreview').style.backgroundImage = '';
                document.getElementById('avatarPreview').innerHTML = '<?php echo $first_letter; ?>';
                // Можно добавить скрытое поле для отметки удаления фото
            }
        }
    </script>

<?php
include(__DIR__ . '/tpl/footer.php');

?>
