# Apartment Rental Search

Apartment Rental Search with Elasticsearch

Each apartment is defined by a set of attributes, such as the number of bedrooms, bathrooms, and other features. It also has a base nightly price, which can be adjusted by applying price modifiers, either as a fixed amount or a percentage. All prices are indexed in Elasticsearch for each apartment, allowing efficient filtering based on criteria such as price range, number of nights, and start date. Occupied dates are automatically excluded from the pricing data, so there's no need for additional availability filters, price checks inherently account for availability. Price sorting functionality also works seamlessly with all applied price filters.

[Front-end app repository](https://github.com/dam-bal/apartment-rental-search-app)

## Tech used
* Elasticsearch
* Laravel
* [https://github.com/dam-bal/eloquentity](https://github.com/dam-bal/eloquentity) - used to map eloquent models to entities
* [https://github.com/dam-bal/elasticsearch-query-builder)](https://github.com/dam-bal/elasticsearch-query-builder) - used to build elasticsearch query
* [https://github.com/elastic/elasticsearch-php](https://github.com/elastic/elasticsearch-php)

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
