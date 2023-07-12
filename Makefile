# Variables d'environnement
DB_NAME = app
DB_USER = app
DB_PASS = !ChangeMe!

launch: ## Lance les conteneurs Docker
	docker-compose up -d --build

down: ## Arrête les conteneurs Docker
	docker-compose down

test: ## Exécute les tests
	docker-compose exec app ./vendor/bin/phpunit

.PHONY: up down build migrate test
