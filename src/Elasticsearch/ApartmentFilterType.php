<?php

namespace Core\Elasticsearch;

enum ApartmentFilterType: string
{
    case MATCH = 'match';
    case RANGE_MIN = 'range_min';
    case RANGE = 'range';
}
