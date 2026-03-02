#TODO
FROM debian:trixie

RUN apt update && \
    apt upgrade -y && \
    apt install git php && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    rm composer-setup.php

RUN git clone https://github.com/lopatar/Denicek.git && cd Denicek && \
    composer install && \
    cp .env.example .env && \
    php artisan migrate && \
    php artisan db:seed

ENTRYPOINT [ "php", "artisan serve" ]
