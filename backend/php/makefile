#!/bin/bash

help: ## Show this help message
	@echo "usage:\n\tmake [target]"
	@echo "targets:"
	@egrep "^(.+)\:\ ##\ (.+)" ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

server: # launch server
	php -S 0.0.0.0:4000 -t ./public

remlogs:  ## borra los logs
	rm -fr logs/*