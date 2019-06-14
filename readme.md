# Platform Backend
[![Drone (cloud)](https://img.shields.io/drone/build/I1820/sjd-backend.svg?style=flat-square)](https://cloud.drone.io/I1820/sjd-backend)

## Introduction
Every microservice architecture must have a glue and its our glue for I1820 Platform.
This service handles authentication, authorization, and acts as a proxy between frontend and other services. It also caches the additional information about projects and things.

## Up and Running
Let's up and run this piece of shit on ubuntu 18.04 with nginx.
First of all you must install the php.

```bash
sudo apt install curl php-cli php-mbstring git unzip
sudo apt install php-fpm php-pear php-dev
sudo apt install php-curl
```

Then you must install the php composer for php package management.

```bash
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

Almost Done! Install the mongodb extension for php with the following command.

```bash
sudo pecl install mongodb
# remember to enable mongodb extension on php.ini [extension=mongodb.so]
```

You have the php environment so let's run this shit and its requirements!

```bash
docker-compose up -d
```

```bash
composer install
php artisan key:generate
php artisan config:cache
php artisan jwt:secret

php artisan migrate:fresh
php artisan db:seed

php artisan serve --host=0.0.0.0 --port=7070
```

## Errors Numbers
sjd-backend returns the following error codes with HTTP 200 OK:

* 701 UnAuthorized
* 704 Not Found
* 706 Already Existed
* 707 Validation Error
