FROM php:8.1-apache

# Устанавливаем необходимые расширения
RUN docker-php-ext-install pdo pdo_pgsql

# Копируем файлы в контейнер
COPY public/ /var/www/html/

# Даем права на запись для загрузки файлов
RUN chmod -R 755 /var/www/html
RUN chmod -R 777 /var/www/html/uploads

# Включаем mod_rewrite для ЧПУ
RUN a2enmod rewrite

# Открываем порт
EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]
