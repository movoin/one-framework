ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

#############################
# Docker machine states
#############################

rebuild:
	docker build -t one/framework $$(pwd)/

up:	clean_runtime clean_ds run

run:
	docker run -it -d --name one_framework -p 9501:9501 -v $$(pwd):/app one/framework

down:
	docker stop one_framework && docker rm one_framework

start:
	docker start one_framework

stop:
	docker stop one_framework

ssh:
	docker exec -it -u app one_framework bash

root:
	docker exec -it one_framework bash

tail:
	docker logs -f one_framework

clean_ds:
	find . -name .DS_Store -print0 | xargs -0 rm -f

clean_runtime:
	rm -f $$(pwd)/example/runtime/**/*.log
	rm -f $$(pwd)/example/runtime/**/*.pid
	rm -f $$(pwd)/example/runtime/**/*.sock

#############################
# Argument fix workaround
#############################
%:
	@:
