<?php

namespace App\Http\Controllers\Admin;

use App\Helper\ImageHelper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'string'
        ]);

        $users = User::where('name', 'LIKE', '%'.$request->input('name').'%')->orderBy('id', 'DESC')->paginate(20);

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

        return response()->json(['code' => 204]);
    }

}
