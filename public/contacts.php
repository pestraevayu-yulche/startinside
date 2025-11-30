<?php
include('tpl/header.php');
?>
    <!-- Фоновая картинка -->
    <div class="background-container">
        <img src="img/contacts.jpg" alt="Фон" class="background-image">
        <div class="overlay"></div>
    </div>
    
    <?php
    include('tpl/nav.php');
    ?>

    <!-- Секция контактов -->
    <div class="contacts-section">
        <div class="contact-container">
            <div class="row align-items-center">
                <!-- Левая часть - название и слоган -->
                <div class="col-lg-6">
                    <div class="company-info">
                        <h1 class="company-title">СтартИнсайт</h1>
                        <p class="company-slogan">Раскрой свой потенциал с искусственным интеллектом</p>
                        <p class="company-description">
                            СтартИнсайт - это инновационная платформа для профессионального самоопределения 
                            и карьерного роста. Мы помогаем людям найти свое призвание, определить сильные 
                            стороны и построить успешную карьеру с помощью передовых технологий 
                            искусственного интеллекта и психометрического тестирования.
                        </p>
                    </div>
                </div>
                
                <!-- Правая часть - контактные данные -->
                <div class="col-lg-6">
                    <div class="contact-info-right">
                        <h2 class="contact-heading">Наши контакты</h2>
                        
                        <div class="contact-item">
                            <div class="contact-details">
                                <h3 class="contact-type">Телефон</h3>
                                <p class="contact-text">+7 (906) 108-62-22</p>
                                <p class="contact-text">+7 (347) 555-22-21</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-details">
                                <h3 class="contact-type">Email</h3>
                                <p class="contact-text">info@startinside.ru</p>
                                <p class="contact-text">support@startinside.ru</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-details">
                                <h3 class="contact-type">Адрес</h3>
                                <p class="contact-text">г. Уфа</p>
                                <p class="contact-text">ул. Комарова, 18</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Форма обратной связи -->
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="form-section">
                        <h2 class="form-title">Оставьте заявку, если есть вопросы</h2>
                        <form method="post" id="contactForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Ваше имя" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Ваш телефон" required>
                                </div>
                            </div>
                            <input type="email" class="form-control" placeholder="Ваш email" required>
                            <textarea class="form-control" rows="5" placeholder="Ваше сообщение" required></textarea>
                            <button type="submit" class="submit-btn">Отправить сообщение</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const inputs = this.querySelectorAll('input[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.border = '2px solid red';
                } else {
                    input.style.border = '';
                }
            });
            
            if (isValid) {
                alert('Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
                this.reset();
            } else {
                alert('Пожалуйста, заполните все обязательные поля.');
            }
        });
    </script>
<?php
include('tpl/footer.php');
?>