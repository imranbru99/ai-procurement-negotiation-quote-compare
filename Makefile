COMPOSE ?= docker compose
APP_SERVICE ?= app

.PHONY: setup up down fresh test lint shell logs build

setup: ## Copy env, build images, install deps, migrate & seed
	@test -f .env || cp .env.example .env
	$(COMPOSE) build
	$(COMPOSE) up -d
	$(COMPOSE) exec $(APP_SERVICE) composer install
	$(COMPOSE) exec $(APP_SERVICE) php artisan key:generate --force
	$(COMPOSE) exec $(APP_SERVICE) php artisan migrate --seed --force
	$(COMPOSE) exec $(APP_SERVICE) npm ci
	$(COMPOSE) exec $(APP_SERVICE) npm run build

up: ## Start all Compose services
	$(COMPOSE) up -d --build

down: ## Stop and remove Compose services
	$(COMPOSE) down

fresh: ## Recreate DB schema and reseed
	$(COMPOSE) exec $(APP_SERVICE) php artisan migrate:fresh --seed --force

test: ## Run the Pest/PHPUnit test suite
	$(COMPOSE) exec $(APP_SERVICE) php artisan test

lint: ## Run Pint (test mode) and PHPStan when configured
	$(COMPOSE) exec $(APP_SERVICE) vendor/bin/pint --test
	@if [ -f phpstan.neon ] || [ -f phpstan.neon.dist ]; then \
		$(COMPOSE) exec $(APP_SERVICE) vendor/bin/phpstan analyse; \
	fi

shell: ## Open a shell in the app container
	$(COMPOSE) exec $(APP_SERVICE) bash

logs: ## Tail Compose logs
	$(COMPOSE) logs -f --tail=200

build: ## Build images only
	$(COMPOSE) build
