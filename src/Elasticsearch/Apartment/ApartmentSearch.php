<?php

namespace Core\Elasticsearch\Apartment;

use Elastic\Elasticsearch\Response\Elasticsearch;
use InvalidArgumentException;
use Spatie\ElasticsearchQueryBuilder\Builder;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\NestedQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\RangeQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\TermQuery;
use Spatie\ElasticsearchQueryBuilder\Sorts\NestedSort;
use Spatie\ElasticsearchQueryBuilder\Sorts\Sort;
use Spatie\ElasticsearchQueryBuilder\Sorts\Sorting;

class ApartmentSearch
{
    public const PER_PAGE = 12;
    private const PAGE = 1;
    private const SORT_DEFAULT_ORDER = Sorting::ASC;
    private const FIELDS = [
        'bedrooms',
        'bathrooms',
        'guests',
        'petsAllowed',
        'id',
        'name',
    ];

    /** @var array<string, BoolQuery> */
    private array $nestedFilters;

    public function __construct(
        private readonly Builder $builder,
        private readonly array $config,
        private readonly array $sortConfig = []
    ) {
    }

    public function search(array $parameters): Elasticsearch
    {
        $page = max(($parameters['page'] ?? self::PAGE) - 1, 0);
        $perPage = $parameters['perPage'] ?? self::PER_PAGE;
        $sort = $parameters['sort'] ?? null;

        $query = new BoolQuery();

        $fields = self::FIELDS;

        $this->nestedFilters = [];

        foreach ($parameters as $param => $value) {
            $config = $this->config[$param] ?? null;

            if (!$config || empty($value)) {
                continue;
            }

            if ($config['nested'] ?? null) {
                $this->processNested($query, $config, $value);
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

    private function processNested(BoolQuery $query, array $config, mixed $value): void
    {
        $nestedFilter = $this->nestedFilters[$config['nested']['group']] ?? null;

        if (!$nestedFilter) {
            $nestedFilter = $this->createNestedFilter($query, $config);
        }

        $this->process($nestedFilter, $config, $value);
    }

    private function createNestedFilter(BoolQuery $query, array $config): BoolQuery
    {
        $nestedFilter = new BoolQuery();

        $this->nestedFilters[$config['nested']['group']] = $nestedFilter;

        $nestedQuery = new NestedQuery($config['nested']['path'], $nestedFilter);

        $nestedQuery->innerHits(NestedQuery\InnerHits::create($config['nested']['group'])->size(1));

        $query->add($nestedQuery, 'filter');

        return $nestedFilter;
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

            $isNested = $config['nested'] ?? null;

            if ($isNested) {
                $this->processNestedSort($field, $config, $order);
            } else {
                $this->builder->addSort(Sort::create($field, $order));
            }
        }
    }

    private function processNestedSort(string $field, array $config, string $order): void
    {
        $nestedSort = NestedSort::create($config['nested']['path'], $field, $order);

        $nestedSort->maxChildren(1);

        if (($config['nested']['group'] ?? null) && $this->nestedFilters[$config['nested']['group']] ?? null) {
            $nestedSort->filter($this->nestedFilters[$config['nested']['group']]);
        }

        if ($config['nested']['mode'] ?? null) {
            $nestedSort->mode($config['nested']['mode']);
        }

        $this->builder->addSort($nestedSort);
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
