FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    php7.2 php7.2-common php7.2-mbstring php-bcmath php7.2-zip php7.2-curl php7.2-xml \
    composer

RUN apt-get install -y nano curl

RUN useradd -mU appuser

RUN mkdir -p /var/www
RUN chown appuser:appuser /var/www

USER appuser

COPY --chown=appuser . /var/www

WORKDIR /var/www

RUN composer install

CMD [ "php7.2", "-S", "0.0.0.0:8080", "-t", "web" ]

EXPOSE 8080
