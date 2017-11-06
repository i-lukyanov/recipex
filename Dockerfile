FROM phusion/baseimage:0.9.19

ENV LANG       en_US.UTF-8
ENV LC_ALL     en_US.UTF-8

CMD ["/sbin/my_init"]

# installing packages
RUN add-apt-repository -y ppa:ondrej/php \
    && add-apt-repository -y ppa:nginx/stable \
    && DEBIAN_FRONTEND="noninteractive" apt-get update \
    && DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes \
        wget \
        curl \
        git \
        php7.0-cli \
        php7.0-curl \
        php7.0-fileinfo \
        php7.0-fpm \
        php7.0-gd \
        php7.0-gmp \
        php7.0-intl \
        php7.0-json \
        php7.0-mbstring \
        php7.0-mcrypt \
        php7.0-pgsql \
        php7.0-xdebug \
        php7.0-xml \
        php7.0-zip \
        jq \
        unzip \
        nginx


# misc commands and configs
RUN sed -i "s:.*date.timezone =.*:date.timezone = Europe/Moscow:" /etc/php/7.0/fpm/php.ini \
    && sed -i "s:.*date.timezone =.*:date.timezone = Europe/Moscow:" /etc/php/7.0/cli/php.ini \
    && echo "daemon off;" >> /etc/nginx/nginx.conf \
    && sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.0/fpm/php-fpm.conf \
    && sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini \
    && sed -i -e "s/#\sserver_names_hash_bucket_size\s64;/server_names_hash_bucket_size 128;/g" /etc/nginx/nginx.conf \
    && mkdir -p /var/www /etc/my_init.d /etc/service/nginx /etc/service/phpfpm /data/config /run/php /etc/nginx/ssl

# conf files
COPY server/sites-enabled/* /etc/nginx/sites-enabled/
COPY server/custom-nginx.conf /etc/nginx/conf.d/custom-nginx.conf
COPY server/pool.d/x-custom.conf /etc/php/7.0/fpm/pool.d/x-custom.conf

# services
COPY server/nginx.sh  /etc/service/nginx/run
COPY server/phpfpm.sh /etc/service/phpfpm/run
COPY server/ssl/* /etc/nginx/ssl/

# startup scripts
COPY ./scripts/02_enable-xdebug-if-dev.sh /etc/my_init.d/02_enable-xdebug-if-dev.sh

WORKDIR /var/www
ADD . .

# permissions and owner files changes
RUN chmod +x /etc/service/nginx/run \
    && chmod +x /etc/service/phpfpm/run \
    && chmod +x ./scripts/* \
    && chown -R root:root /var/log/nginx \
    && chown -R www-data:www-data . 

EXPOSE 80
EXPOSE 443

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /etc/nginx/sites-enabled/default
