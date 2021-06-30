FROM ubuntu

RUN apt update -y

ARG DEBIAN_FRONTEND=noninteractive
RUN apt install apache2 -y

RUN apt install php libapache2-mod-php -y

CMD apachectl -D FOREGROUND