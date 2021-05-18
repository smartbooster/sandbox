# Variables
ENV?=dev
SYMFONY=php bin/console
SHELL = /bin/sh
HOST_USER := $(shell id -u)
HOST_GROUP := $(shell id -g)
export HOST_USER
export HOST_GROUP
APPLICATION := smartbooster-sandbox

.DEFAULT_GOAL := help
.PHONY: help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' ./Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[34m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[34m##/[33m/'
	@echo ''
	@echo '\033[02mTo launch and install the project use the command\033[0m'"\033[01m\033[34m make up\033[0m"', then in another terminal type\033[0m'"\033[01m\033[34m make ssh\033[0m"' followed by\033[0m'"\033[01m\033[34m make install \033[0m"''

##
## Install
## -------
.PHONY: install
install: ## Install the project
	yarn install
ifeq ($(ENV),dev)
	composer install
	./bin/phpunit install
	make assets-dev
else
	composer install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction
	make assets-build
endif
	make orm-install


##
## Development
## -----------
.PHONY: debug-env
debug-env: ## Display environment variables used in the container
	$(SYMFONY) debug:container --env-vars --env=$(ENV)

.PHONY: cc
cc: ## Cache clear symfony
	$(SYMFONY) cache:clear --env=$(ENV)
	$(SYMFONY) cache:warmup

##
## Database management
## -------------------
.PHONY: orm-install
orm-install: ## Create the databse + Loading minimales fixtures. For production environnement add ENV=PROD
	$(SYMFONY) doctrine:database:drop --if-exists --force --env=$(ENV)
	$(SYMFONY) doctrine:database:create --env=$(ENV)
	$(SYMFONY) doctrine:schema:create --env=$(ENV)
	$(SYMFONY) doctrine:migration:version --add --all --no-interaction --env=$(ENV)
	make orm-load-minimal
	make orm-load-fake

.PHONY: orm-status
orm-status: ## Show ORM status (Migrations, Mapping, Synch). For production environnement add ENV=PROD
	$(SYMFONY) doctrine:migrations:status --no-interaction --env=$(ENV)
	$(SYMFONY) doctrine:schema:validate --env=$(ENV)

.PHONY: orm-show-diff
orm-show-diff: ## Dump the SQL needed to update the database schema to match the current mapping metadata.  For production environnement add ENV=PROD
	$(SYMFONY) doctrine:schema:update --dump-sql --env=$(ENV)

.PHONY: orm-diff
orm-diff: ## Generate a migration by comparing your current database to your mapping information.
	$(SYMFONY) doctrine:migra:diff --no-interaction --env=$(ENV)

.PHONY: orm-load-minimal
orm-load-minimal: ## Load fixtures from the minimal group to the database
	$(SYMFONY) doctrine:fixtures:load --group=minimal --no-interaction --env=$(ENV)

.PHONY: orm-load-fake
orm-load-fake: ## Load fixtures from the fake group to the database
	$(SYMFONY) doctrine:fixtures:load --group=fake --append --no-interaction --env=$(ENV)

##
## Assets
## ------
.PHONY: assets-bundle
assets-bundle:
	$(SYMFONY) assets:install --symlink --relative --env=$(ENV)

.PHONY: assets-dev ad
assets-dev: assets-bundle ## Compile the assets in dev mode. Shortcut command "make ad"
assets-dev:
	yarn run dev
ad: assets-dev

.PHONY: assets-watch aw
assets-watch: ## Enable the watcher on the assets. Shortcut command "make aw"
	yarn run watch
aw: assets-watch

.PHONY: assets-build ab
assets-build: override ENV=prod ## Compile the assets in production mode. Shortcut command "make ab"
assets-build: assets-bundle
assets-build:
	yarn run build
ab: assets-build

##
## Tests
## -----
.PHONY: phpunit
phpunit: ## Launch all tests
	XDEBUG_MODE=coverage ./bin/phpunit --coverage-text

##
## Qualimetry
## ----------
.PHONY: checkstyle cs
checkstyle: ## PHP Checkstyle
	vendor/bin/phpcs --extensions=php -n --standard=PSR12 --report=full src tests
cs: checkstyle

.PHONY: code-beautifier cb
code-beautifier: ## Code beautifier (Checkstyle fixer)
	vendor/bin/phpcbf --extensions=php --standard=PSR12 src tests
cb: code-beautifier

.PHONY: lint-php lint-twig lint-yaml lint-xliff lint-container
lint-php: ## Linter PHP
	find config src -type f -name "*.php" -exec php -l {} \;
lint-twig: ## Linter Twig
	find templates -type f -name "*.twig" | xargs $(SYMFONY) lint:twig
lint-yaml: ## Linter Yaml
	$(SYMFONY) lint:yaml config
lint-xliff: ## Linter Xliff for translations
	$(SYMFONY) lint:xliff translations
lint-container: ## Linter Container service definitions
	$(SYMFONY) lint:container --no-debug

.PHONY: composer-validate
composer-validate: ## Validate composer.json and composer.lock
	composer validate composer.json

.PHONY: metrics
metrics: ## Build static analysis from the php in src. Repports available in ./build/index.html
	cd src && ../vendor/bin/phpmetrics --report-html=../build/phpmetrics.html .

.PHONY: phpstan
phpstan: ## Launch PHP Static Analysis
	vendor/bin/phpstan analyse src tests --level=6 -c phpstan.neon

.PHONY: qualimetry qa
qualimetry: checkstyle lint-php lint-twig lint-yaml lint-xliff lint-container composer-validate metrics phpstan ## Launch all qualimetry rules. Shortcut "make qa"
qa: qualimetry

.PHONY: cpd
cpd: ## Copy paste detector
	vendor/bin/phpcpd --fuzzy src

##
## Docker commands
## ---------------
.PHONY: up
up: ## Start the project stack with docker
	docker-compose up

down: ## Kill the project stack with docker
	docker-compose down

ps: ## List containers from project
	docker-compose ps

ssh: ## Access to the php container in interactive mode
	docker exec -it --user=dev $(APPLICATION)-docker-php bash

nginx: ## Access to the nginx container in interactive mode
	docker exec -it --user=www-data ${APPLICATION}-docker-nginx bash

mysql: ## Access to the mysql container in interactive mode
	docker exec -it --user=mysql ${APPLICATION}-docker-mysql bash
