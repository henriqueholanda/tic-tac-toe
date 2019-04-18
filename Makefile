.DEFAULT_GOAL := test

start:
	docker-compose up -d
.PHONY: start

stop:
	docker-compose down
.PHONY: stop

test:
	docker-compose run --rm api ./vendor/bin/phpunit
.PHONY: test

coverage:
	docker-compose run --rm api ./vendor/bin/phpunit --coverage-html var/coverage/
.PHONY: coverage

lint:
	docker-compose run --rm api ./vendor/bin/phpstan analyse src/ tests/
.PHONY: lint