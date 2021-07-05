FROM ubuntu:18.04

ARG DEBIAN_FRONTEND=noninteractive

#installing dependencies
RUN apt update -y && \
    apt install software-properties-common -y && \
    LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php -y && \
    apt install php7.1 php7.1-common php7.1-cli php7.1-xml apache2 curl unzip -y

#instaling composer
RUN \
    cd ~ && \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    HASH=`curl -sS https://composer.github.io/installer.sig` && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY ./ /var/www/html

RUN touch /etc/apache2/sites-available/dev.com.conf && \
    cat dev_host > /etc/apache2/sites-available/dev.com.conf && \
    a2ensite dev.com.conf && \
    a2dissite 000-default.conf && \
    service apache2 restart

CMD apachectl -D FOREGROUND