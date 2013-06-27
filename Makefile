path 		= src
unitpath 	=

help:
	@echo "make install - for jamwork in Vendor folder"
	@echo "make phpunit"
	@echo "make phpcs"
	@echo "make lint"

install:
	@echo "Aktualisiere Jamwork"
	@if [ -d Vendor/jamwork/.git ]; then \
		echo "install:\n\t git pull" > Vendor/jamwork/Makefile ;\
		$(MAKE) -C Vendor/jamwork ;\
		rm Vendor/jamwork/Makefile ;\
	else\
		git clone git@repo.dreiwerken.intern:frameworks/jamwork.git Vendor/jamwork ;\
	fi

phpunit:
	@phpunit $(unitpath)

phpcs:
	@phpcs --standard=./build/phpcs.xml -p $(path)

lint:
	@echo "Syntaxchecker $(path)"
	@find $(path) -name *.php -exec php -l '{}' \; > lint.txt
	@rm lint.txt
