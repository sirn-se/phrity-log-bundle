# Default
all: deps-install


# DEPENDENCY MANAGEMENT
# Updates dependencies accoring to lock file
deps-install: composer.phar
	./composer.phar --no-interaction install

# Updates dependencies accoring to json file
deps-update: composer.phar
	./composer.phar self-update
	./composer.phar --no-interaction update

# Updates dependencies accoring to lock file, production optimized
deps-prod: composer.phar clear-assets
	./composer.phar --no-interaction install --no-dev --optimize-autoloader


# TESTS AND REPORTS
# Code standard check
cs-check: composer.lock
	./vendor/bin/phpcs --standard=PSR1,PSR2 --encoding=UTF-8 --report=full --colors src tests

# Run tests
test: composer.lock
	./vendor/bin/phpunit

# Run tests with clover coverage report
coverage-clover: composer.lock
	./vendor/bin/phpunit --coverage-clover build/logs/clover.xml
	./vendor/bin/coveralls -v

# Run tests with html coverage report
coverage-html: composer.lock
	./vendor/bin/phpunit --coverage-html build/html

# INITIAL INSTALL
# Ensures composer is installed
composer.phar:
	curl -sS https://getcomposer.org/installer | php

# Ensures composer is installed and dependencies loaded
composer.lock: composer.phar
	./composer.phar --no-interaction install