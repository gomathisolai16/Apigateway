FROM 493356om/:latest

WORKDIR /var/www/
#copy /app into docker container
COPY . .
#COPY . /var/www/
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer update && composer install

RUN php artisan passport:keys
RUN php artisan migrate --seed
RUN chmod -R 777 /var/www/storage

# Enable headers module
RUN a2enmod rewrite headers
