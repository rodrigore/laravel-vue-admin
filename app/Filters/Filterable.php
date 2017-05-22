<?php

namespace App\Filters;

use App\Filters\QueryFilter;

trait Filterable
{
    public function scopeFilter($builder, QueryFilter $queryFilter)
    {
        return $queryFilter->apply($builder);
    }
}
