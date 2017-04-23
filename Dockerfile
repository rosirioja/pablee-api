FROM debian:jessie

RUN apt-get update && apt-get -y install php5 libapache2-mod-php5 php5-mcrypt php5-pgsql php5-mysql php5-curl curl git rsyslog vim nano --fix-missing

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN mkdir -p /pablee
WORKDIR /pablee

COPY .env.docker /pablee/.env 
COPY . /pablee

RUN cd /pablee && composer -n install --no-plugins --no-scripts

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]
