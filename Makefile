# Default
all: deps-install


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

cs-check: composer.phar
	./vendor/bin/phpcs --standard=PSR1,PSR2 --encoding=UTF-8 --report=full --colors src tests

test: composer.phar
	./vendor/bin/phpunit tests/

coverage: composer.phar
	./vendor/bin/phpunit --coverage-clover build/logs/clover.xml tests/
	./vendor/bin/phpcov merge --clover build/logs/clover.xml build/cov

# Installs and/or updates composer.phar
composer.phar:
	curl -sS https://getcomposer.org/installer | php
