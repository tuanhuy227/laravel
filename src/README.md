docker compose up -d

composer install
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

php artisan key:generate
php artisan migrate --seed
php artisan storage:link

php artisan test

XDEBUG_MODE=coverage php -d memory_limit=2G \
    vendor/bin/phpunit \
    --coverage-html reports/coverage-html

composer require maatwebsite/excel:^3.1

php artisan make:import ProductsImport --model=Product

rm -rf vendor composer.lock
composer clear-cache
composer require maatwebsite/excel:^3.1

php artisan test --filter=ProductTest

