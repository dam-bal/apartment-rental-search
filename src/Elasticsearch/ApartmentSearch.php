<?php

namespace Core\Elasticsearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use InvalidArgumentException;
use Spatie\ElasticsearchQueryBuilder\Builder;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\RangeQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\TermQuery;

readonly class ApartmentSearch
{
    private const PAGE = 0;
    private const PER_PAGE = 12;

    public function __construct(
        private Builder $builder,
        private array $config
    ) {
    }

    public function search(array $parameters): Elasticsearch
    {
        $page = $parameters['page'] ?? self::PAGE;
        $perPage = $parameters['perPage'] ?? self::PER_PAGE;

        $query = new BoolQuery();

        foreach ($parameters as $param => $value) {
            $config = $this->config[$param] ?? null;

            if (!$config) {
                continue;
            }

            $this->process($query, $config, $value);
        }

        return $this->builder
            ->from($page * $perPage)
            ->size($perPage)
            ->addQuery($query)
            ->search();
    }

    private function process(BoolQuery $query, array $config, mixed $value): void
    {
        $type = $config['type'] ?? throw new InvalidArgumentException('Type is required');
        $field = $config['field'] ?? throw new InvalidArgumentException('Field is required');

        if ($type === ApartmentFilterType::MATCH) {
            $query->add(TermQuery::create($field, $value));
        }

        if ($type === ApartmentFilterType::RANGE_MIN) {
            $query->add(RangeQuery::create($field)->gte($value));
        }
    }
}
