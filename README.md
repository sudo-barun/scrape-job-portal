# Scrape Job Portals

## Pre-requisites:

1. PHP, Composer
1. Node
1. OpenSSL (version 1.1.1 or above)
1. Docker (optional)

## Setup
1. `composer install`
1. `npm ci`
1. `npm run prod`
1. Copy `.env.example` to `.env`
1. Update `.env` if required

## Usage
1. `php console.php scrape`
1. `php -S 0.0.0.0:8080 -t web`
1. Open http://127.0.0.1:8080 in browser

## Docker usage

1. Copy `/docker/.env.sample` to `/docker/.env`
1. Update `/docker/.env` if required
1. `cd docker`
1. `docker-compose build`
1. `cd docker`
1. `docker-compose up`
