COMPOSE ?= docker compose
PHP ?= $(COMPOSE) exec php
CONSOLE ?= $(PHP) php bin/console
PHP_IMAGE ?= workspace-php

.PHONY: up down build bash logs composer-install migrate migrate-status migrate-rollback cache-clear test about schema-validate

up:
	$(COMPOSE) up -d --build

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

bash:
	$(PHP) bash

logs:
	$(COMPOSE) logs -f --tail=100

composer-install:
	docker run --rm -v $(CURDIR):/var/www/html -w /var/www/html -e COMPOSER_ALLOW_SUPERUSER=1 --network bridge $(PHP_IMAGE) composer install --no-interaction --prefer-dist

migrate:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

migrate-status:
	$(CONSOLE) doctrine:migrations:status

migrate-rollback:
	$(CONSOLE) doctrine:migrations:migrate prev --no-interaction

cache-clear:
	$(CONSOLE) cache:clear

test:
	$(PHP) vendor/bin/phpunit

about:
	$(CONSOLE) about

schema-validate:
	$(CONSOLE) doctrine:schema:validate
