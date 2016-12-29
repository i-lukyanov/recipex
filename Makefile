# without this target, the unnecessary output will be done
.SILENT: help build up down clear

COLOUR=\033[30;01m
END=\033[0m

help:
	printf "Project management entrypoint\n\n" && \
    printf "$(COLOUR)make build$(END)      building & running from scratch\n" && \
    printf "$(COLOUR)make up$(END)         turn on the project without building\n" && \
    printf "$(COLOUR)make down$(END)       turn off the project\n" && \
    printf "$(COLOUR)make clear$(END)      clear all the caches\n"

build:
	docker-compose build \
    && docker-compose up -d \
    && ./tools/install.sh \

up:
	docker-compose up -d \
    && ./tools/install.sh \

down:
	docker-compose down

clear:
	sudo rm -rf var/cache/*
