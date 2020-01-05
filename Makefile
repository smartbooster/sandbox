ENV?=dev

## Cache clear
cc:
	bin/console --env=$(ENV) cache:clear

## Database install
orm.install:
	bin/console --env=$(ENV) doctrine:database:drop --if-exists --force
	php bin/console doctrine:database:create --env=$(ENV)
	php bin/console doctrine:schema:create --env=$(ENV)
	make load.fixtures

load.fixtures:
	php bin/console doctrine:fixtures:load --no-interaction --env=$(ENV)

## Install and compile assets
assets:
	php bin/console assets:install --symlink --relative --env=$(ENV)

dev.assets: assets
dev.assets:
	 yarn run encore dev

deploy.assets: assets
deploy.assets:
	 yarn run encore production

install:
	 composer install
	 make dev.assets
	 make orm.install

# ====================
## Docker

SHELL = /bin/sh

HOST_USER := $(shell id -u)
HOST_GROUP := $(shell id -g)

export HOST_USER
export HOST_GROUP

APPLICATION := smartbooster-sandbox

up:
	docker-compose up

down:
	docker-compose down

ps:
	docker-compose ps

ssh:
	docker exec -it $(APPLICATION)-docker-php bash

nginx:
	docker exec -it --user=www-data ${APPLICATION}-docker-nginx bash

mysql:
	docker exec -it --user=mysql ${APPLICATION}-docker-mysql bash