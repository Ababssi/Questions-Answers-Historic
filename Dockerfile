FROM php:8.2-apache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql intl

# Add Git and Unzip
RUN apt-get update && apt-get install -y \
    git \
    unzip

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

COPY . /var/www/

RUN mkdir -p /var/www/exportFiles && chmod -R 777 /var/www/exportFiles

COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/

ENTRYPOINT ["bash", "./docker.sh"]

EXPOSE 80
