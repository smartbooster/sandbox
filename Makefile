ENV?=dev

## Cache clear
cc:
	bin/console --env=$(ENV) cache:clear
	bin/console cache:warmup

cct: override ENV=test
cct: cc

## Database install
orm.install:
	bin/console --env=$(ENV) doctrine:database:drop --if-exists --force
	php bin/console doctrine:database:create --env=$(ENV)
	php bin/console doctrine:schema:create --env=$(ENV)
	make orm.load-minimal
	make orm.load-fake

orm.status:
	php bin/console doctrine:schema:validate --env=$(ENV)

### Minimal
orm.load-minimal:
	php bin/console doctrine:fixtures:load --group=minimal --no-interaction --env=$(ENV)

orm.load-fake:
	php bin/console doctrine:fixtures:load --group=fake --append --no-interaction --env=$(ENV)


## Install and compile assets
assets:
	php bin/console assets:install --symlink --relative --env=$(ENV)

dev.assets: assets
dev.assets:
	 yarn run encore dev

dev.assets-watch: assets
dev.assets-watch:
	yarn run encore dev --watch
daw: dev.assets-watch

deploy.assets: assets
deploy.assets:
	 yarn run encore production

install:
	 composer install
	 yarn install
	 make dev.assets
	 make orm.install

# ====================
# Qualimetry rules

cs: checkstyle
checkstyle:
	vendor/bin/phpcs --extensions=php -n --standard=PSR12 --report=full src tests

lint.php:
	find config src -type f -name "*.php" -exec php -l {} \;

lint.twig:
	find templates -type f -name "*.twig" | xargs php bin/console lint:twig

lint.yaml:
	php bin/console lint:yaml config

lint.xliff:
	php bin/console lint:xliff translations

lint.container:
	php bin/console lint:container


composer.validate:
	composer validate composer.json

qa: qualimetry
qualimetry: checkstyle lint.php lint.twig lint.yaml lint.xliff lint.container composer.validate metrics phpstan

## Qualimetry : code-beautifier
cb: code-beautifier
code-beautifier:
	vendor/bin/phpcbf --extensions=php --standard=PSR12 src tests

cpd:
	vendor/bin/phpcpd --fuzzy src

metrics:
	vendor/bin/phpmetrics --report-html=build/phpmetrics.html src

phpstan:
	vendor/bin/phpstan analyse src --level=6 -c phpstan.neon

## Test : phpunit
phpunit:
	vendor/bin/phpunit tests

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
	docker exec -it --user=dev $(APPLICATION)-docker-php bash

nginx:
	docker exec -it --user=www-data ${APPLICATION}-docker-nginx bash

mysql:
	docker exec -it --user=mysql ${APPLICATION}-docker-mysql bash
