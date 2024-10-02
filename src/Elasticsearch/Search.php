<?php

namespace Core\Elasticsearch;

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

class Search
{
    public const PER_PAGE = 12;
    private const PAGE = 1;
    private const SORT_DEFAULT_ORDER = Sorting::ASC;

    /** @var array<string, BoolQuery> */
    private array $nestedFilters;

    /**
     * @param Builder $builder
     * @param array<string, array> $parameterConfig
     * @param array<string, array> $sortConfig
     * @param array<string, array> $config
     */
    public function __construct(
        private readonly Builder $builder,
        private readonly array $parameterConfig = [],
        private readonly array $sortConfig = [],
        private readonly array $config = []
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     * @return Elasticsearch
     */
    public function search(
        array $parameters = [],
        int $page = self::PAGE,
        array $sort = [],
        int $perPage = self::PER_PAGE
    ): Elasticsearch {
        $query = new BoolQuery();

        $this->nestedFilters = [];

        foreach ($parameters as $param => $value) {
            $config = $this->parameterConfig[$param] ?? null;

            if (!$config || empty($value)) {
                continue;
            }

            $nested = $config['nested'] ?? null;

            if ($nested) {
                $nestedFilter = $this->nestedFilters[$config['nested']['group']] ??
                    $this->createNestedFilter($query, $config);

                $this->processParameter($nestedFilter, $config, $value);
            }

            if (!$nested) {
                $this->processParameter($query, $config, $value);
            }
        }

        if (!empty($query->toArray()['bool'])) {
            $this->builder->addQuery($query);
        }

        $this->processSort($sort);

        $this->builder->from(max($page - 1, 0) * $perPage);
        $this->builder->size($perPage);

        if (!empty($this->config['fields'])) {
            $this->builder->fields($this->config['fields']);
        }

        return $this->builder->search();
    }

    private function processParameter(BoolQuery $query, array $parameterConfig, mixed $value): void
    {
        $type = $parameterConfig['type'] ?? throw new InvalidArgumentException('Type is required');
        $field = $parameterConfig['field'] ?? throw new InvalidArgumentException('Field is required');

        if ($type === FilterType::MATCH) {
            $query->add(TermQuery::create($field, $value));
        }

        if ($type === FilterType::RANGE) {
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

    private function processSort(array $sortOptions): void
    {
        foreach ($sortOptions as $sortOption) {
            $sortValues = explode(':', $sortOption);

            $key = $sortValues[0];

            $config = $this->sortConfig[$key] ?? null;
            if (!$config) {
                return;
            }

            $order = $sortValues[1] ?? $config['order'] ?? self::SORT_DEFAULT_ORDER;
            $field = $config['field'];

            $nested = $config['nested'] ?? null;
            if ($nested) {
                $this->processNestedSort($field, $config, $order);
            }

            if (!$nested) {
                $this->builder->addSort(Sort::create($field, $order));
            }
        }
    }

    private function processNestedSort(string $field, array $sortConfig, string $order): void
    {
        $nestedConfig = $sortConfig['nested'];
        $group = $nestedConfig['group'] ?? null;

        $nestedSort = NestedSort::create($nestedConfig['path'], $field, $order);

        $nestedSort->maxChildren($nestedConfig['maxChildren'] ?? 1);

        if ($group && isset($this->nestedFilters[$group])) {
            $nestedSort->filter($this->nestedFilters[$group]);
        }

        if ($mode = $nestedConfig['mode'] ?? null) {
            $nestedSort->mode($mode);
        }

        $this->builder->addSort($nestedSort);
    }

    private function createNestedFilter(BoolQuery $query, array $config): BoolQuery
    {
        $nestedConfig = $config['nested'];
        $group = $nestedConfig['group'];

        $nestedFilter = new BoolQuery();

        $this->nestedFilters[$group] = $nestedFilter;

        $nestedQuery = new NestedQuery($nestedConfig['path'], $nestedFilter);

        $nestedQuery
            ->innerHits(NestedQuery\InnerHits::create($group)
                ->size($this->config['innerHits'][$group]['size'] ?? 1)
            );

        $query->add($nestedQuery, $this->config['innerHits'][$group]['queryType'] ?? 'filter');

        return $nestedFilter;
    }
}
