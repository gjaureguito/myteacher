FROM php:8.3-apache

# Extensiones que usa la aplicación; la imagen base no incluye cURL ni mbstring.
RUN apt-get update \
 && apt-get install -y --no-install-recommends libonig-dev libcurl4-openssl-dev \
 && docker-php-ext-install pdo_mysql opcache mbstring curl \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers remoteip \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && printf '<Directory /var/www/html/public>\n  AllowOverride All\n  Require all granted\n</Directory>\nServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/app.conf \
 && a2enconf app

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/start.sh /usr/local/bin/start.sh
RUN sed -i 's/\r$//' /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data storage

CMD ["start.sh"]
