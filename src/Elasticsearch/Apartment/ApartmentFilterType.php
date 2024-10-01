<?php

namespace Core\Elasticsearch\Apartment;

enum ApartmentFilterType: string
{
    case MATCH = 'match';
    case RANGE_MIN = 'range_min';
    case RANGE = 'range';
}
