FROM php:8.3-apache

# Python é usado pelos scripts de análise (IA) em /scripts
RUN apt-get update \
    && apt-get install -y --no-install-recommends python3 python3-requests \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# O site é servido a partir de /public (app, config, .env ficam fora da web)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && printf '<Directory ${APACHE_DOCUMENT_ROOT}>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        > /etc/apache2/conf-available/investegra.conf \
    && a2enconf investegra

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
