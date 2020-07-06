bash-db:
	docker-compose exec db bash

bash-webserver:
	docker-compose exec webserver bash

bash-app:
	docker-compose exec app bash

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

docker-rebuild: docker-down docker-build docker-up

app-init:
	cp .env.example .env
	docker-compose exec db bash /init_mysql.sh
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate

app-composer-install:
	docker-compose exec app composer install

app-composer-update:
	docker-compose exec app composer update

app-tinker:
	docker-compose exec app php artisan tinker
