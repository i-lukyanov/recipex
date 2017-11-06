#!/bin/bash
if [ "$ENVIRONMENT" == "dev" ]; then
    cp /var/www/server/conf.d/20-xdebug.ini /etc/php/7.0/fpm/conf.d/
    cp /var/www/server/conf.d/20-xdebug.ini /etc/php/7.0/cli/conf.d/
else
    # disable xdebug if not dev env
    rm /etc/php/7.0/fpm/conf.d/20-xdebug.ini && rm /etc/php/7.0/cli/conf.d/20-xdebug.ini
fi

service php7.0-fpm reload
