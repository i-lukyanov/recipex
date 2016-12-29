#!/bin/sh

cd /var/www
php /var/www/vendor/brianlmoon/gearmanmanager/pecl-manager.php -vvv -c /var/www/app/config/worker_config.ini
