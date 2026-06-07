# Usar la imagen oficial de PHP 8.2 con el servidor Apache
FROM php:8.2-apache
 
# Habilitar módulos de Apache necesarios
RUN a2enmod rewrite headers
 
# Instalar las extensiones de PDO y MySQL
RUN docker-php-ext-install pdo pdo_mysql
 
# Copiar todo el código a la carpeta pública de Apache
COPY . /var/www/html/
 
# Dar permisos al servidor web
RUN chown -R www-data:www-data /var/www/html/
 
# Exponer el puerto 80
EXPOSE 80
 