<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        return \App\User::
            when(request()->name, function ($query) {
                $name = request()->name;
                $query->where('name', 'ilike', "%$name%");
            })
            ->when(request()->sex != '', function ($query) {
                $values =  explode(',', request()->sex);
                $query->whereIn('sex', $values);
            })
            ->when(request()->sortBy, function ($query) {
                list($column, $order)  = explode(',', request()->sortBy);
                if ($column) {
                    $query->orderBy($column, $order);
                }
            })
            ->paginate();
    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:4',
            'sex' => 'required',
            'age' => 'required|integer|min:1',
            'birth' => 'required',
            'address' => 'required',
        ]);

        \App\User::create(request()->all());
    }
}
