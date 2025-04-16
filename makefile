
.PHONY: up down test coverage

up:
	docker-compose up -d
migrate:
	docker exec -it transacoes-simplificadas.app php artisan migrate
seed:
	docker exec -it transacoes-simplificadas.app php artisan db:seed
down:
	docker-compose down
test:
	docker-compose exec transacoes-simplificadas.app ./vendor/bin/phpunit
test-coverage:
	docker-compose exec transacoes-simplificadas.app ./vendor/bin/phpunit --coverage-html build/coverage
composer-install:
	docker-compose exec transacoes-simplificadas.app composer install
composer-update:
	docker-compose exec transacoes-simplificadas.app composer update
swagger-generate:
	docker exec -it transacoes-simplificada.app php artisan l5-swagger:generate
