<?php

namespace Core\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;

class Index
{
    public function __construct(
        private readonly string $indexName,
        private readonly Client $client
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function create(array $body): Elasticsearch
    {
        return $this->client->indices()->create(
            [
                'index' => $this->indexName,
                'body' => $body,
            ]
        );
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function index(string|int $id, array $body): Elasticsearch
    {
        return $this->client->index(
            [
                'id' => $id,
                'index' => $this->indexName,
                'body' => $body,
            ]
        );
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function update(string|int $id, array $body): Elasticsearch
    {
        return $this->client->update(
            [
                'id' => $id,
                'index' => $this->indexName,
                'body' => $body,
            ]
        );
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(array $body): Elasticsearch
    {
        return $this->client->search(
            [
                'index' => $this->indexName,
                'body' => $body
            ]
        );
    }
}
