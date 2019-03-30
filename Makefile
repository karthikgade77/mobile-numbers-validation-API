container=php

composer-install:
	composer install --ignore-platform-reqs

start:
	docker-compose up -d

build:
	docker-compose build

build-clean:
	docker-compose build --no-cache

stop:
	docker-compose down

space:
	docker rmi $$(docker images --filter "dangling=true" -q --no-trunc); docker rm $$(docker ps -qa --no-trunc --filter "status=exited"); docker volume rm $$(docker volume ls -qf dangling=true);

FILE=
test:
	docker-compose run php ./vendor/bin/codecept run $(FILE)

