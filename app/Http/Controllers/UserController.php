<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
      $users = User::all();
      return response()->json($users);
    }

    public function store(Request $request)
    {
      $user = new User;
      $user->fill($request->all());
      $user->password = Hash::make($request->input('password'));
      $user->save();
      return response()->json($user);
    }

    public function edit($id)
    {
      $user = User::findOrFail($id);
      return response()->json($user);
    }

    public function update(Request $request, $id)
    {
      $user = User::findOrFail($id);
      $user->fill($request->all());
      $user->password = Hash::make($request->input('password'));
      $user->save();
      return response()->json($user);
    }

    public function destroy($id)
    {
      $user = User::findOrFail($id);
      $user->delete();
      return response()->json('deleted');
    }

}
