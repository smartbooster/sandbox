# Variables
ENV?=dev
CONSOLE=php bin/console
APPLICATION := smartbooster-sandbox

include make/*.mk

##
## Installation and update
## -------
.PHONY: install
install: ## Install the project
	yarn install
ifeq ($(ENV),dev)
	composer install
	make assets-dev
else
	composer install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction
	make assets-build
endif
	make orm-install
ifeq ($(ENV),dev)
	make orm-load-fake
endif

.PHONY: update
update: cc orm-update orm-status
	$(CONSOLE) smart:parameter:load

##
## Development
## -----------
.PHONY: debug-env
debug-env: ## Display environment variables used in the container
	$(CONSOLE) debug:container --env-vars --env=$(ENV)

.PHONY: cc
cc: ## Cache clear symfony
	$(CONSOLE) cache:clear --env=$(ENV)
	$(CONSOLE) cache:warmup
