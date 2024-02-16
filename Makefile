SHELL := /bin/bash

DOCKER = docker compose exec
BACKEND = php
FRONT = node
DB = database
DB_NAME = db_name

###> DOCKER
up:
	$(DOCKER) compose up -d
init:
	docker compose run --rm --no-deps $(FRONT) bash -ci 'yarn install'
	docker compose up -d
	make update
	make ddb
	make migrate

###> COMPOSER
install:
	$(DOCKER) $(BACKEND) composer install
update:
	$(DOCKER) $(BACKEND) composer update

###> DATABASE AND MIGRATIONS
db:
	$(DOCKER) $(BACKEND) php bin/console doctrine:database:create
migration:
	$(DOCKER) $(BACKEND) php bin/console make:migration
migrate:
	$(DOCKER) $(BACKEND) php bin/console doctrine:migrations:migrate
generate:
	$(DOCKER) $(BACKEND) php bin/console doctrine:migrations:generate

###> ASSETS
build:
	$(DOCKER) $(FRONT) yarn encore dev
watch:
	$(DOCKER) $(FRONT) yarn encore dev --watch
yarn:
	$(DOCKER) $(FRONT) yarn install
###> SYMFONY
%:
	$(DOCKER) $(BACKEND) php bin/console make:$@
cc:
	$(DOCKER) $(BACKEND) php bin/console c:c
assets-install:
	$(DOCKER) $(BACKEND) php bin/console assets:install

###> TOOLS
cs-fixer:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
phpunit:
	rm -f tests.txt
	$(DOCKER) $(BACKEND) ./vendor/bin/phpunit > tests.txt

###> CUSTOM COMMANDS
secret-regenerate:
	$(DOCKER) $(BACKEND) php bin/console secret:regenerate-app-secret .env.local

###> UTILS
own:
	sudo chown pdud:pdud src -R
	sudo chown pdud:pdud config -R
	sudo chown pdud:pdud migrations -R
	git add *