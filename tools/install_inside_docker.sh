#!/bin/bash

composer_install=1;
create_db=1;
migrations=1;
load_fixtures=1;
import_chain_urls=1;
npm_clean=1;
npm_install=1;
npm_build=1;

while test $# -gt 0; do
    case "$1" in
        -h|--help)
            echo "install inside docker"
            echo " "
            echo "install_inside_docker.sh [options] [arguments]"
            echo " "
            echo "options:"
            echo "-h, --help                   show brief help"
            echo "--composer-install=[1/0]     run composer install"
            echo "--create-db=[1/0]            create postgres database"
            echo "--migrations=[1/0]           run migrations"
            echo "--load-fixtures=[1/0]        load database fixtures"
            echo "--import-chain-urls=[1/0]    import chain urls"
            echo "--npm-clean=[1/0]            run 'npm run cleanup'"
            echo "--npm-install=[1/0]          run 'npm install'"
            echo "--npm-build=[1/0]            run 'npm build'"
            exit 0
            ;;
        --composer-install*)
            composer_install=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --create-db*)
            create_db=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --migrations*)
            migrations=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --load-fixtures*)
            load_fixtures=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --import-chain-urls*)
            import_chain_urls=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --npm-install*)
            npm_install=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --npm-clean*)
            npm_clean=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        --npm-build*)
            npm_build=`echo $1 | sed -e 's/^[^=]*=//g'`
            shift
            ;;
        *)
            break
            ;;
        esac
done

function checkLastCommandIsSuccessful {
    ERRORCODE=$?
    if [ $ERRORCODE -gt 0 ]; then
	errorMessage "Docker actions script failed, error code $ERRORCODE, exiting"
	exit 1
    fi
}

function debugMessage {
    echo -e "\033[1;93m*** $1 ***\033[00m"
}

function errorMessage {
    echo -e "\033[1;91m*** $1 ***\033[00m"
}

if [ $composer_install -gt 0 ]; then
    debugMessage "Running Composer Install"
    SYMFONY_ENV=development php7.0 /var/www/composer.phar install
    checkLastCommandIsSuccessful
else
    debugMessage "Running Composer Install - SKIPPED"
fi

if [ $create_db -gt 0 ]; then
    debugMessage "Create postgres database"
    EXEC1=`SYMFONY_ENV=development php7.0 /var/www/bin/console doctrine:database:create --no-interaction`

    if [ $? -gt 0 ]; then
	EXEC2=`echo "$EXEC1" |grep "already exists"`
	if [ -z "$EXEC2" ]; then
	    echo "$EXEC1"
	    errorMessage "Cannot create database, exiting"
	    exit 1
	else
	    debugMessage "database already exists"
	fi
    fi
else
    debugMessage "Create postgres database - SKIPPED"
fi

if [ $migrations -gt 0 ]; then
    debugMessage "Running migrations"
    SYMFONY_ENV=development php7.0 /var/www/bin/console doctrine:migrations:migrate --no-interaction
    checkLastCommandIsSuccessful
else
    debugMessage "Running migrations - SKIPPED"
fi

if [ $load_fixtures -gt 0 ]; then
    debugMessage "Loading database fixtures"
    SYMFONY_ENV=development php7.0 /var/www/bin/console doctrine:fixtures:load -n
    checkLastCommandIsSuccessful
else
    debugMessage "Loading database fixtures - SKIPPED"
fi

if [ $import_chain_urls -gt 0 ]; then
    debugMessage "Importing chain urls"
    SYMFONY_ENV=development php7.0 /var/www/bin/console seo:import-chain-urls
    checkLastCommandIsSuccessful
else
    debugMessage "Importing chain urls - SKIPPED"
fi

debugMessage "Importing order counter stuff"
SYMFONY_ENV=development php7.0 /var/www/bin/console dailycounter:import-orders-info
checkLastCommandIsSuccessful

if [ $npm_clean -gt 0 ]; then
    debugMessage "Preparing frontend (Node.js: cleanup)"
    cd /var/www/src/DeliveryClub/FrontendBundle/Resources/assets
    /usr/bin/npm run cleanup
    checkLastCommandIsSuccessful
else
    debugMessage "Preparing frontend (Node.js: cleanup) - SKIPPED"
fi

if [ $npm_install -gt 0 ]; then
    debugMessage "Preparing frontend (Node.js: install modules)"
    /usr/bin/npm install
    checkLastCommandIsSuccessful
else
    debugMessage "Preparing frontend (Node.js: install modules) - SKIPPED"
fi

if [ $npm_build -gt 0 ]; then
    debugMessage "Preparing frontend (Node.js: build & webpack)"
    /usr/bin/npm run build
    checkLastCommandIsSuccessful
else
    debugMessage "Preparing frontend (Node.js: build & webpack) - SKIPPED"
fi

debugMessage "Flushing redis cache"
SYMFONY_ENV=development php7.0 /var/www/bin/console frontend:doctrine:cache:clear-provider api_cache_proxy
checkLastCommandIsSuccessful
SYMFONY_ENV=development php7.0 /var/www/bin/console frontend:doctrine:cache:clear-provider jms_serializer
checkLastCommandIsSuccessful
