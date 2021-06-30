FROM ubuntu

RUN apt update -y

ARG DEBIAN_FRONTEND=noninteractive

#installing dependencies
RUN apt install apache2 curl php php-cli unzip libapache2-mod-php -y

#instaling composer
RUN \
    cd ~ && \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    HASH=`curl -sS https://composer.github.io/installer.sig` && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

CMD apachectl -D FOREGROUND