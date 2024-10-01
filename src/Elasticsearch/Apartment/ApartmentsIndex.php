<?php

namespace Core\Elasticsearch\Apartment;

use Core\Elasticsearch\Index;
use Elastic\Elasticsearch\Client;

class ApartmentsIndex extends Index
{
    private const INDEX_NAME = 'apartments';

    public function __construct(Client $client)
    {
        parent::__construct(self::INDEX_NAME, $client);
    }
}
