.SILENT:

include .env

dc = docker-compose -p ${APP_NAME}

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

node_bash:
	$(dc) exec $(node) bash

restart:
	$(dc) restart

fixtures:
	$(dc) run --rm $(php) php bin/console doctrine:fixtures:load --no-interaction