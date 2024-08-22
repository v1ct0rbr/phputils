# Use a imagem oficial do PHP 8 com Apache
FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Copie a pasta "app" para o diretório do servidor web
COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80 para acesso externo
EXPOSE 80

# Inicie o servidor Apache
CMD ["apache2-foreground"]