# About GoodShoes

## Introduction:

This Rest API was created for a Shoes Story with Laravel 8.83.27.

To build this project, follow the instructions bellow.

You can use Postman app to test the requests for API, just download and import the file "Good Shoes.postman_collection" with all requests calls.

To execute any requests groups, first complete the instalation and log in with "Auth" > "Login User" resquest in Postman.


## Instalation on Windows:

1) Download and extract the PHP in your preferred location through the link: https://windows.php.net/downloads/releases/php-8.1.0-nts-Win32-vs16-x64.zip

2) Open the preferred location/php.ini, find ";extension=fileinfo" and ";extension=pdo_sqlite", remove ";" and save file

3) Add preferred location to PATH in environment variables

4) Download and install the Composer through the link: https://getcomposer.org/Composer-Setup.exe

4) In root of project, find "./.env-example" and copy to "./.env"

5) Access the directory "./good-shoes-laravel-api" in CMD:

```bash
cd ./good-shoes-laravel-api
```

6) Run the commands bellow in order:

```bash
composer install
```

```bash
php artisan key:generate
```

```bash
php artisan migrate
```

```bash
php artisan passport:install --force
```

6) Populate the database with seeds:

```bash
php artisan db:seed
```

7) Start server with command:

```bash
php artisan serve
```
