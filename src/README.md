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

XDEBUG_MODE=coverage php artisan test --coverage-html=storage/coverage-html

docker run -d --name sonarqube \
  -p 9000:9000 \
  sonarqube:community

sonar-scanner \
  -Dsonar.projectKey=project-laravel \
  -Dsonar.sources=. \
  -Dsonar.host.url=http://localhost:9000 \
  -Dsonar.token=sqa_b87cf3abd9ff3153e4440427ddabb750dd31ebe0

php artisan make:migration create_types_table --create=types

php artisan make:seeder TypeSeeder

php artisan db:seed

# Project info
sonar.projectKey=laravel-app
sonar.projectName=Laravel App
sonar.projectVersion=1.0

# Source code
sonar.sources=app
sonar.exclusions=vendor/**,storage/**,node_modules/**

# Tests
sonar.tests=tests
sonar.test.inclusions=tests/**/*.php

# Test coverage
sonar.php.coverage.reportPaths=storage/coverage.xml

# Encoding
sonar.sourceEncoding=UTF-8

php artisan make:model Post -m        # Tạo model + migration
php artisan make:controller PostController --resource
php artisan make:request StorePostRequest
php artisan make:middleware CheckRole
php artisan make:job ProcessPostJob
php artisan make:event PostCreated
php artisan make:listener SendNotificationListener
php artisan make:seeder UserSeeder
php artisan make:factory UserFactory
php artisan make:migration add_status_to_products_table --table=products
