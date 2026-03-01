FROM php:8.2-apache

# install PDO MySQL for database connection
RUN docker-php-ext-install pdo pdo_mysql

# copy project files to server
COPY . /var/www/html/

# set correct permissions
RUN chown -R www-data:www-data /var/www/html/

# enable apache rewrite module
RUN a2enmod rewrite

EXPOSE 80
