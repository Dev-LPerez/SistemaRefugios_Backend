# Usar la imagen oficial de PHP 8.2 con el servidor Apache
FROM php:8.2-apache

# Habilitar el módulo rewrite de Apache (vital para tus URLs amigables del .htaccess)
RUN a2enmod rewrite

# Instalar las extensiones de PDO y MySQL para que PHP pueda hablar con Aiven
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo tu código a la carpeta pública de Apache en el contenedor
COPY . /var/www/html/

# Dar permisos al servidor web para que pueda leer los archivos
RUN chown -R www-data:www-data /var/www/html/

# Exponer el puerto 80 (Render lo detectará automáticamente)
EXPOSE 80