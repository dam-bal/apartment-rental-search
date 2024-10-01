# Apartment Rental Search

Apartment Rental Search with Elasticsearch

[Front-end app repository](https://github.com/dam-bal/apartment-rental-search-app)

## Installation

### Install Dependencies
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### Start
```shell
./vendor/bin/sail up -d
```

### Migrate and Seed
```shell
./vendor/bin/sail migrate
./vendor/bin/sail db:seed
```

### Setup Elasticsearch
```shell
./vendor/bin/sail artisan app:create-apartments-elasticsearch-index
./vendor/bin/sail artisan app:ingest-apartments-to-elasticsearch-index
```

### Start queue worker
```shell
./vendor/bin/sail queue:work
```

## Kibana

Local kibana can be accessed [here](http://localhost:5601/)
