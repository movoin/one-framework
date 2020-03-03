ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

#############################
# Docker machine states
#############################

rebuild:
	docker build -t one/example $$(pwd)/

up:	clean_runtime clean_ds run

run:
	docker run -it -d --name one_example  -p 80:80 -p 9501:9501 -v $$(pwd):/app one/example

down:
	docker stop one_example && docker rm one_example

start:
	docker start one_example

stop:
	docker stop one_example

ssh:
	docker exec -it -u app one_example bash

root:
	docker exec -it one_example bash

tail:
	docker logs -f one_example

clean_ds:
	find . -name .DS_Store -print0 | xargs -0 rm -f

clean_runtime:
	rm -f $$(pwd)/runtime/**/*.log
	rm -f $$(pwd)/runtime/**/*.pid
	rm -f $$(pwd)/runtime/**/*.sock

#############################
# Argument fix workaround
#############################
%:
	@:
