# Platform Backend

## Introduction
Every microservice architecture must have a glue and its our glue for I1820 Platform.
This service handles authentication, authorization, and acts as a proxy between frontend and other services.

## Installation (without Docker)
Let's up and run this piece of shit on ubuntu 18.04 with nginx.
First of all you must install the php.

```bash
sudo apt install curl php-cli php-mbstring git unzip
sudo apt install php-fpm php-pear php-dev
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
```

## Installation (with Docker)
Let's up and run this piece of shit on Docker.

```bash
docker-compose build
docker-compose up
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
```

## Errors Numbers
sjd-backend returns the following error codes:

* 701 UnAuthorized     
* 704 Not Found
* 706 Already Existed  
* 707 Validation Error
