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
	docker exec -it --user=www-data $(APPLICATION)-docker-nginx bash

mysql: ## Access to the mysql container in interactive mode
	docker exec -it --user=mysql $(APPLICATION)-docker-mysql bash
