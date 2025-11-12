# Використовуємо офіційний PHP-образ з Apache
FROM php:8.2-apache

# Оновлення пакетів та встановлення залежностей для PHP-розширень
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Вмикаємо mod_rewrite
RUN a2enmod rewrite

# Копіюємо сайт
COPY ./src /var/www/html

# Встановлюємо права
RUN chown -R www-data:www-data /var/www/html

# Папка сайту
WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]