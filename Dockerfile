FROM php:8.3-apache AS php

RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get --assume-yes install \
    libicu-dev libonig-dev libxml2-dev \
  && docker-php-ext-install intl mbstring pdo_mysql simplexml \
  && rm --recursive --force /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN ln --symbolic /var/run/mysqld/mysqld.sock /tmp/mysql.sock

FROM php AS composer

RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get --assume-yes install \
    git unzip \
  && rm --recursive --force /var/lib/apt/lists/* \
  && curl https://raw.githubusercontent.com/composer/getcomposer.org/main/web/installer | php -- \
    --2 --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["composer"]

FROM php AS cakephp

RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get --assume-yes install \
    ruby-dev \
  && gem install --no-document --version='>= 0.8, < 0.9' mailcatcher \
  && rm --recursive --force /var/lib/apt/lists/*

RUN echo 'php_admin_value sendmail_path "/usr/bin/env catchmail"' > /etc/apache2/conf-available/catchmail.conf
RUN a2enconf catchmail
