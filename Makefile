path 		= src
unitpath 	=

help:
	@echo "make install - for jamwork in Vendor folder"
	@echo "make phpunit"
	@echo "make phpcs"
	@echo "make lint"

install:
	@echo "Aktualisiere Jamwork"
	@if [ -d Vendor/Jamwork/.git ]; then \
		echo "install:\n\t git pull" > Vendor/Jamwork/Makefile ;\
		$(MAKE) -C Vendor/Jamwork ;\
		rm Vendor/Jamwork/Makefile ;\
	else\
		git clone git@repo.dreiwerken.intern:frameworks/jamwork.git Vendor/Jamwork ;\
	fi

phpunit:
	@phpunit $(unitpath)

phpcs:
	@phpcs --standard=./build/phpcs.xml -p $(path)

lint:
	@echo "Syntaxchecker $(path)"
	@find $(path) -name *.php -exec php -l '{}' \; > lint.txt
	@rm lint.txt
