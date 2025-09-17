fe:
npm i
npm start

be:

docker compose up -d


docker compose exec -it app bash

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