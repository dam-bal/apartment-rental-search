<?php

namespace Core\Elasticsearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use InvalidArgumentException;
use Spatie\ElasticsearchQueryBuilder\Builder;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\NestedQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\RangeQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\TermQuery;
use Spatie\ElasticsearchQueryBuilder\Sorts\Sort;

readonly class ApartmentSearch
{
    private const PAGE = 1;
    private const PER_PAGE = 12;
    private const SORT_DEFAULT_ORDER = Sort::ASC;
    private const FIELDS = [
        'bedrooms',
        'bathrooms',
        'guests',
        'petsAllowed',
        'id',
        'name',
    ];

    public function __construct(
        private Builder $builder,
        private array $config,
        private array $sortConfig = []
    ) {
    }

    public function search(array $parameters): Elasticsearch
    {
        $page = max(($parameters['page'] ?? self::PAGE) - 1, 0);
        $perPage = $parameters['perPage'] ?? self::PER_PAGE;
        $sort = $parameters['sort'] ?? null;

        $query = new BoolQuery();

        $fields = self::FIELDS;

        $nestedFilters = [];

        foreach ($parameters as $param => $value) {
            $config = $this->config[$param] ?? null;

            if (!$config || empty($value)) {
                continue;
            }

            if ($config['nested'] ?? null) {
                $nestedFilter = $nestedFilters[$config['nested']['group']] ?? null;

                if (!$nestedFilter) {
                    $nestedFilter = new BoolQuery();

                    $nestedFilters[$config['nested']['group']] = $nestedFilter;

                    $nestedQuery = new NestedQuery($config['nested']['path'], $nestedFilter);

                    $nestedQuery->innerHits(NestedQuery\InnerHits::create($config['nested']['group'])->size(1));

                    $query->add($nestedQuery, 'filter');
                }

                $this->process($nestedFilter, $config, $value);
            } else {
                $this->process($query, $config, $value);
            }

            $fields = array_merge($fields, $config['fields'] ?? []);
        }

        if (!empty($query->toArray()['bool'])) {
            $this->builder->addQuery($query);
        }

        if ($sort) {
            $this->processSort($sort);
        }

        return $this->builder
            ->from($page * $perPage)
            ->size($perPage)
            ->fields(array_values(array_unique($fields)))
            ->search();
    }

    private function processSort(string $sort): void
    {
        $sortOptions = explode(',', $sort);

        foreach ($sortOptions as $sortOption) {
            $sortData = explode(':', $sortOption);

            $key = $sortData[0];

            $config = $this->sortConfig[$key] ?? null;

            $order = $sortData[1] ?? $config['order'] ?? self::SORT_DEFAULT_ORDER;

            if (!$config) {
                return;
            }

            $field = $config['field'];

            $this->builder->addSort(Sort::create($field, $order));
        }
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

        if ($type === ApartmentFilterType::RANGE) {
            $values = explode(',', $value);

            $range = RangeQuery::create($field);

            if ($values[0] !== '') {
                $range->gte($values[0]);
            }

            if (isset($values[1]) && $values[1] !== '') {
                $range->lte($values[1]);
            }

            $query->add($range);
        }
    }
}
