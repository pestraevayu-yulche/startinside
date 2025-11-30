FROM php:8.1-apache

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Копируем содержимое public в корень Apache
COPY public/ /var/www/html/

# Создаем папки и настраиваем права
RUN mkdir -p /var/www/html/uploads/avatars && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/uploads

# Включаем mod_rewrite
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]
