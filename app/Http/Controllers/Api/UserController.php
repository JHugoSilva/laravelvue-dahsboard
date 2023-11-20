<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $users = User::latest()->get();
        // $users = User::latest()->get()->map(function($user) {
        //     return [
        //         'id' => $user->id,
        //         'name' => $user->name,
        //         'email' => $user->email,
        //         'created_at' => $user->created_at->toFormattedDate()
        //     ];
        // });
        return response()->json($users, 200);
    }

    public function store() {
       
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        return response()->json($user, 201);
    }

    public function update($id) {

        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'password' => 'sometimes|min:8'
        ]);

        
        $user = User::find($id);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password
        ]);

        return $user;
    }

    public function destroy($id) {
        $user = User::find($id);
        $user->delete();
        return response()->noContent();
    }
}
