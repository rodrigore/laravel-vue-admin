<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    public function name($value = null)
    {
        return $this->builder->where('name', 'like', "%$value%");
    }

    public function sex($value = '')
    {
        $values =  explode(',', $value);

        return $this->builder->whereIn('sex', $values);
    }

    public function sortBy($sortBy)
    {
        list($column, $order)  = explode(',', $sortBy);

        if ($column) {
            return $this->builder->orderBy($column, $order);
        }
    }
}
