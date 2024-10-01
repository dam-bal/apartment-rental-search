<?php

namespace Core\Elasticsearch;

enum FilterType: string
{
    case MATCH = 'match';
    case RANGE = 'range';
}
