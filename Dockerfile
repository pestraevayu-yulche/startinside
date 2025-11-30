FROM php:8.1-apache

# Устанавливаем системные зависимости для PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP для PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Копируем файлы в контейнер
COPY public/ /var/www/html/

# Даем права на запись для загрузки файлов
RUN chmod -R 755 /var/www/html
RUN mkdir -p /var/www/html/uploads/avatars && chmod -R 777 /var/www/html/uploads

# Включаем mod_rewrite для ЧПУ
RUN a2enmod rewrite

# Открываем порт
EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]
