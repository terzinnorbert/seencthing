FROM ubuntu:latest

MAINTAINER Terzin Norbert <terzin.norbert@gmail.com>

RUN export DEBIAN_FRONTEND=noninteractive && apt-get update && apt-get install -y software-properties-common && \
    LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php && apt-get update && apt-get install -y \
    curl acl git unzip \
    apache2 libapache2-mod-php7.2 php7.2 php7.2-xml php7.2-gd php7.2-opcache php7.2-mbstring \
    php7.2-mysql php7.2-tidy php7.2-curl php7.2-sqlite3 php7.2-xdebug \
    gnupg2 && curl -sL https://deb.nodesource.com/setup_9.x | bash -  && \
    apt-get install -y nodejs && \
    curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && \
    a2enmod rewrite expires

COPY docker/docker-cmd /usr/local/bin/
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
WORKDIR /var/www/html
EXPOSE 80

CMD ["docker-cmd"]