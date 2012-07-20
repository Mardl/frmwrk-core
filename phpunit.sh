echo Coverage
phpunit --coverage-html html/coverage


echo Checkstyle
phpcs --report=checkstyle --report-file=html/codesniffer/report --standard=DREIWerken --extensions=php --tab-width=4 --ignore=\.git,*/framework/jamwork/*,*/misc/*,*/Vendor/*,*/framework/Doctrine/* .
