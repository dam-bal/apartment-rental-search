<?php

namespace Core\Elasticsearch;

class ResponseProcessor
{
    public function process(array $elasticsearchResponse): ProcessedResponse
    {
        $results = [];

        foreach ($elasticsearchResponse['hits']['hits'] ?? [] as $hit) {
            $resultItem = $hit['_source'];

            foreach ($hit['inner_hits'] ?? [] as $key => $innerHit) {
                $innerHits = $innerHit['hits']['hits'] ?? [];

                if (count($innerHits)) {
                    $resultItem[$key] = $innerHit['hits']['hits'][0]['_source'];
                } else {
                    $resultItem[$key] = null;
                }
            }

            $results[] = $resultItem;
        }

        return new ProcessedResponse($elasticsearchResponse['hits']['total']['value'] ?? 0, $results ?? []);
    }
}
