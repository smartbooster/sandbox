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
