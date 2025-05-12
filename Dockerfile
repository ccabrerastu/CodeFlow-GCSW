# Imagen base con PHP y Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para URLs amigables en Apache
RUN a2enmod rewrite

# Instalar extensiones necesarias para PDO, MySQL y MySQLi
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copiar archivos del proyecto a la carpeta del servidor
COPY . /var/www/html

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
