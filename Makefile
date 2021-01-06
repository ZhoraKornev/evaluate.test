.SILENT:

include .env

dc = docker-compose

web = web
php = php
db = db
queue = rabbit

bridge = 127.0.0.1:${NGINX_EXPOSE_PORT}

http_address = "http://$(bridge)"

build:
	$(dc) up --build --force-recreate -d
	echo $(http_address)

start:
	$(dc) start
	echo $(http_address)

stop:
	$(dc) stop

down:
	$(dc) down

logs:
	$(dc) logs

logs_f:
	$(dc) logs -f

ps:
	$(dc) ps

php_bash:
	$(dc) exec $(php) bash

db_bash:
	$(dc) exec $(db) bash

restart:
	$(dc) restart

fixtures:
	$(dc) exec -T $(php) php bin/console doctrine:fixtures:load --no-interaction

cache-clear:
	$(dc) exec -T $(php) php bin/console doctrine:cache:clear-metadata

migrations:
	$(dc) exec -T $(php) php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction;

tests:
	$(dc) exec -T $(php) php bin/phpunit;

linter-run:
	$(dc) exec -T $(php) php ./vendor/bin/phplint ./ --exclude=vendor;
