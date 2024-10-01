<?php

namespace Core\Elasticsearch;

readonly class ProcessedResponse
{
    /**
     * @param mixed[] $results
     */
    public function __construct(
        public int $total,
        public array $results
    ) {
    }
}
