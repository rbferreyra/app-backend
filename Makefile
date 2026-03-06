# ─────────────────────────────────────────────
# RealEstate — app-backend Makefile
# ─────────────────────────────────────────────

DOCKER_COMPOSE = docker compose
APP_CONTAINER  = realestate-app
PHP            = $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php
COMPOSER       = $(DOCKER_COMPOSE) exec $(APP_CONTAINER) composer
ARTISAN        = $(PHP) artisan

.DEFAULT_GOAL := help

# ─────────────────────────────────────────────
# Help
# ─────────────────────────────────────────────
.PHONY: help
help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'

# ─────────────────────────────────────────────
# Docker
# ─────────────────────────────────────────────
.PHONY: up
up: ## Start all containers (detached)
	$(DOCKER_COMPOSE) up -d --build

.PHONY: down
down: ## Stop and remove containers
	$(DOCKER_COMPOSE) down

.PHONY: restart
restart: down up ## Restart all containers

.PHONY: build
build: ## Rebuild images without cache
	$(DOCKER_COMPOSE) build --no-cache

.PHONY: ps
ps: ## Show running containers
	$(DOCKER_COMPOSE) ps

.PHONY: logs
logs: ## Tail logs from all containers
	$(DOCKER_COMPOSE) logs -f

.PHONY: logs-app
logs-app: ## Tail logs from app container only
	$(DOCKER_COMPOSE) logs -f $(APP_CONTAINER)

# ─────────────────────────────────────────────
# Shell access
# ─────────────────────────────────────────────
.PHONY: shell
shell: ## Open shell inside app container
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) bash

.PHONY: shell-root
shell-root: ## Open root shell inside app container
	$(DOCKER_COMPOSE) exec --user root $(APP_CONTAINER) bash

.PHONY: shell-db
shell-db: ## Open MySQL shell
	$(DOCKER_COMPOSE) exec realestate-db mysql -u forge -psecret realestate

.PHONY: shell-redis
shell-redis: ## Open Redis CLI
	$(DOCKER_COMPOSE) exec realestate-redis redis-cli -a secret

# ─────────────────────────────────────────────
# Laravel install (first-time setup)
# ─────────────────────────────────────────────
.PHONY: install
install: ## Install Laravel into existing directory (first time only)
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) bash -c \
		"composer create-project laravel/laravel:^12.0 /tmp/laravel && cp -rn /tmp/laravel/. /var/www/ && rm -rf /tmp/laravel"
	cp .env.example .env
	$(ARTISAN) key:generate
	$(ARTISAN) migrate

.PHONY: setup
setup: ## Full first-time setup (copy .env + generate key + migrate)
	cp .env.example .env
	$(ARTISAN) key:generate
	$(ARTISAN) migrate --seed

# ─────────────────────────────────────────────
# Composer
# ─────────────────────────────────────────────
.PHONY: composer-install
composer-install: ## composer install
	$(COMPOSER) install

.PHONY: composer-update
composer-update: ## composer update
	$(COMPOSER) update

.PHONY: require
require: ## Install a package: make require pkg=vendor/package
	$(COMPOSER) require $(pkg)

# ─────────────────────────────────────────────
# Artisan
# ─────────────────────────────────────────────
.PHONY: artisan
artisan: ## Run artisan command: make artisan cmd="route:list"
	$(ARTISAN) $(cmd)

.PHONY: migrate
migrate: ## Run migrations
	$(ARTISAN) migrate

.PHONY: migrate-fresh
migrate-fresh: ## Fresh migration with seeders
	$(ARTISAN) migrate:fresh --seed

.PHONY: seed
seed: ## Run database seeders
	$(ARTISAN) db:seed

.PHONY: rollback
rollback: ## Rollback last migration
	$(ARTISAN) migrate:rollback

.PHONY: cache-clear
cache-clear: ## Clear all Laravel caches
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear
	$(ARTISAN) event:clear

.PHONY: optimize
optimize: ## Optimize for production
	$(ARTISAN) config:cache
	$(ARTISAN) route:cache
	$(ARTISAN) view:cache
	$(ARTISAN) event:cache

.PHONY: queue-work
queue-work: ## Start queue worker manually
	$(ARTISAN) queue:work --verbose

.PHONY: horizon
horizon: ## Start Laravel Horizon
	$(ARTISAN) horizon

# ─────────────────────────────────────────────
# Testing
# ─────────────────────────────────────────────
.PHONY: test
test: ## Run PHPUnit tests
	$(PHP) artisan test

.PHONY: test-filter
test-filter: ## Run specific test: make test-filter f=AuthTest
	$(PHP) artisan test --filter=$(f)

.PHONY: test-coverage
test-coverage: ## Run tests with coverage report
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php -d xdebug.mode=coverage artisan test --coverage

# ─────────────────────────────────────────────
# Code Quality
# ─────────────────────────────────────────────
.PHONY: pint
pint: ## Run Laravel Pint (code formatter)
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) ./vendor/bin/pint

.PHONY: pint-test
pint-test: ## Check code style without fixing
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) ./vendor/bin/pint --test