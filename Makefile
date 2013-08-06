path 		= src
unitpath 	=

help:
	@echo "make install - for jamwork in Vendor folder"
	@echo "make phpunit"
	@echo "make phpcs"
	@echo "make lint"

install: composer-install
#	@echo "Aktualisiere Jamwork"
#	@if [ -d Vendor/jamwork/.git ]; then \
#		echo "install:\n\t git pull" > Vendor/jamwork/MakefileTMP ;\
#		$(MAKE) -f MakefileTMP -C Vendor/jamwork ;\
#		rm Vendor/jamwork/MakefileTMP ;\
#	else\
#		git clone git@repo.dreiwerken.intern:frameworks/jamwork.git Vendor/jamwork ;\
#	fi

phpunit:
	@phpunit $(unitpath)

phpcs:
	@phpcs --standard=./build/phpcs.xml --report=summary -p $(path)

lint:
	@echo "Syntaxchecker $(path)"
	@find $(path) -name *.php -exec php -l '{}' \; > lint.txt
	@rm lint.txt

# Composer laden
download-composer:
	@curl -sS https://getcomposer.org/installer | php

# Composer.json für vfsstream erstellen
composer-install: download-composer
	@if [ -f composer.lock ]; then \
		php composer.phar update ;\
	else \
		php composer.phar install ;\
	fi
