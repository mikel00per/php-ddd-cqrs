#!/usr/bin/make

SCRIPT_VERSION=v1.0
SCRIPT_AUTHOR=Antonio Miguel Morillo Chica

HELP_FUN = \
    %help; while(<>){ \
   		push@{$$help{$$2//'options'}},[$$1,$$3] \
    	if/^([\w-_]+)\s*:.*\#\#(?:@(\w+))?\s(.*)$$/ \
	}; \
    print"$$_:\n", map"  $$_->[0]".(" "x(20-length($$_->[0])))."$$_->[1]\n",\
    @{$$help{$$_}},"\n" for keys %help; \

help: ##@Miscellaneous Show this help
	@echo "Usage: make [target] ...\n"
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
	@echo "Written by $(SCRIPT_AUTHOR), version $(SCRIPT_VERSION)"
	@echo "Please report any bug or error to the author."

start: ##@Library Init library rename strings
	sh start.sh

run: ##@Container Build and run php container
	docker-compose -f docker-compose.yml up -d --build

build: ##@Container Build php container
	docker-compose -f docker-compose.yml build

stop: ##@Container Stop php container
	docker-compose -f docker-compose.yml down

destroy: ##@Container Remove all data related with php container
	docker-compose -f docker-compose.yml down --rmi all

shell: ##@Container SHH in container
	docker-compose -f docker-compose.yml exec php /bin/bash

logs: ##@Container Show logs in container
	docker-compose -f docker-compose.yml logs

lint: ##@Style Show style errors
	docker-compose -f docker-compose.yml exec php composer lint

lint-fix: ##@Style Fix style errors
	docker-compose -f docker-compose.yml exec php composer lint:fix

test: ##@Tests Execute tests
	docker-compose -f docker-compose.yml exec php composer test

test-coverage: ##@Tests Execute tests with coverage
	docker-compose -f docker-compose.yml exec php composer test:coverage

exec: ##@Code Execute the code index
	docker-compose -f docker-compose.yml exec php composer exec