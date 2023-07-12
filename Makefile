# Variables d'environnement
DB_NAME = app
DB_USER = app
DB_PASS = ChangeMe

launch: ## Lance les conteneurs Docker
	docker-compose up -d --build

down: ## Arrête les conteneurs Docker
	docker-compose down

install: ## Exécute composer install
	docker-compose exec app composer install

test: ## Exécute les tests
	docker-compose exec app ./vendor/bin/phpunit

.PHONY: launch down install test
