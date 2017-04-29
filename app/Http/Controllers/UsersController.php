<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index()
    {
        return User::
            when(request()->name, function ($query) {
                $name = request()->name;
                $query->where('name', 'like', "%$name%");
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:4',
            'sex' => 'required',
            'age' => 'required|integer|min:1',
            'birth' => 'required|date',
            'address' => 'required',
        ]);

        User::create(request()->all());

        return response()->json([
            'message' => 'Successful created'
        ]);
    }

    public function update(User $user)
    {
        $this->validate(request(), [
            'name' => 'sometimes|required',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'sex' => 'sometimes|required',
            'age' => 'sometimes|required|integer|min:1',
            'birth' => 'sometimes|required|date',
            'address' => 'sometimes|required',
        ]);

        $user->fill(request()->all())->save();

        return response()->json([
            'message' => 'Successful edited'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'The user has been successfully deleted'
        ], Response::HTTP_ACCEPTED);
    }
}
