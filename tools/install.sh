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
            echo "install.sh - installation script of frontend application"
            echo " "
            echo "install.sh [options] [arguments]"
            echo " "
            echo "options:"
            echo "-h, --help                  show brief help"
            echo "-o, --no-composer-install   skip composer install"
            echo "-d, --no-db                 skip postgres actions"
            echo "  -p, --no-create-db          skip create postgres database"
            echo "  -m, --no-migrations         skip run migrations"
            echo "  -f, --no-load-fixtures      skip load database fixtures"
            echo "-u, --no-import-chain-urls  skip import chain urls"
            echo "-n, --no-npm                skip npm actions"
            echo "  -c, --no-npm-clean          skip 'npm run cleanup'"
            echo "  -i, --no-npm-install        skip 'npm install'"
            echo "  -b, --no-npm-build          skip 'npm build'"
            exit 0
            ;;
        -o|--no-composer-install)
	    composer_install=0;
            shift
            ;;
        -d|--no-db)
	    create_db=0;
	    migrations=0;
	    load_fixtures=0;
            shift
            ;;
        -p|--no-create-db)
	    create_db=0;
            shift
            ;;
        -m|--no-migrations)
	    migrations=0;
            shift
            ;;
        -f|--no-load-fixtures)
	    load_fixtures=0;
            shift
            ;;
        -u|--no-import-chain-urls)
	    import_chain_urls=0;
            shift
            ;;
        -n|--no-npm)
	    npm_clean=0;
	    npm_install=0;
	    npm_build=0;
            shift
            ;;
        -c|--no-npm-clean)
	    npm_clean=0;
            shift
            ;;
        -i|--no-npm-install)
	    npm_install=0;
            shift
            ;;
        -b|--no-npm-build)
	    npm_build=0;
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
	errorMessage "Install script failed, error code $ERRORCODE, exiting"
	exit 1
    fi
}

function clearSymfonyCacheFolder {
    cd "$DIR/.."
    rm -rf var/cache/* &>/dev/null
    CACHEFILES=`ls var/cache |wc -l`

    if [ $CACHEFILES -gt 0 ]; then
	echo "*** Please enter password to clear cache folder"
	sudo rm -rf var/cache/* &>/dev/null
    fi
}

function debugMessage {
    echo -e "\033[1;93m*** $1 ***\033[00m"
}

function errorMessage {
    echo -e "\033[1;91m*** $1 ***\033[00m"
}

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

WEBCONTAINER=`docker ps|grep dc-frontend-v2-web-app|cut -f 1 -d" "`
REDISCONTAINER=`docker ps|grep dc-frontend-v2-redis|cut -f 1 -d" "`

if [ -z $WEBCONTAINER ]; then
    errorMessage "Error: Web docker container is not running"
    exit 1
fi

if [ -z $REDISCONTAINER ]; then
    errorMessage "Error: Redis docker container is not running"
    exit 1
fi

if [ $composer_install -gt 0 ]; then
    debugMessage "Preparing to run composer install: adding local ssh key to docker container (for git)"
    docker cp $HOME/.ssh/id_rsa $WEBCONTAINER:/root/.ssh/id_rsa
    checkLastCommandIsSuccessful

    debugMessage "Preparing to run composer install: removing symfony cache folder"
    clearSymfonyCacheFolder
else
    debugMessage "Preparing to run composer install - SKIPPED"
fi

debugMessage "Running actions inside docker"
docker exec -i -t $WEBCONTAINER /bin/bash /var/www/tools/install_inside_docker.sh \
  --composer-install=$composer_install \
  --create-db=$create_db \
  --migrations=$migrations \
  --load-fixtures=$load_fixtures \
  --import-chain-urls=$import_chain_urls \
  --npm-clean=$npm_clean \
  --npm-install=$npm_install \
  --npm-build=$npm_build
checkLastCommandIsSuccessful

debugMessage "Removing symfony cache folder"
clearSymfonyCacheFolder

debugMessage "Install completed successfully"
