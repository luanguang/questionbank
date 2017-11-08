<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'DESC')->paginate(20);

        return response()->json(['users' => $users]);
    }

    public function show($user_id)
    {
        $user = User::findOrFail($user_id);

        return response()->json(['user' => $user]);
    }

    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        return;
    }
}
