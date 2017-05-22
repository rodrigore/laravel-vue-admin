<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (! method_exists($this, $name)) {
                continue;
            }

            if (trim($value) != '') {
                $this->$name($value);
            } else {
                $this->name();
            }
        }
    }

    private function columns($columns = '*')
    {
        return $this->builder->select(explode(',', $columns));
    }

    private function filters()
    {
        return $this->request->all();
    }
}
