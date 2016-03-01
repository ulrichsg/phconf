# vim: tabstop=4:softtabstop=4:shiftwidth=4:noexpandtab

help:
	@echo "Targets:"
	@echo "  test - run test suite"
	@echo "  deps - install dependencies"
	@echo ""
	@exit 0

test:
	@vendor/bin/phpunit

install-composer:
	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer.phar ]; then curl -s http://getcomposer.org/installer | php -n -d allow_url_fopen=1 -d date.timezone="Europe/Berlin" -- --install-dir=./bin/; fi

deps:
	@make install-composer
	@php -n -d allow_url_fopen=1 -d date.timezone="Europe/Berlin" ./bin/composer.phar update --dev

.PHONY: test help deps install-composer
