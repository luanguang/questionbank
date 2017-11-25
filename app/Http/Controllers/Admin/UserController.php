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

        $users = User::withTrashed()->where('name', 'LIKE', '%'.$request->input('name').'%')->orderBy('id', 'DESC')->paginate(20);

        return response()->json(['users' => $users]);
    }

    public function show($user_id)
    {
        $user = User::findOrFail($user_id);

        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $user_id)
    {
        $this->validate($request, [
            'is_admin'      =>  'boolean',
            'profession'    =>  'string|in:teacher,student'
        ]);

        $user = User::findOrFail($user_id);
        $user->update([
            'is_admin'      =>  !empty($request->input('is_admin')) ? $request->input('is_admin') : '0',
            'profession'    =>  $request->input('profession')
        ]);

        return response()->json(['user' => $user]);
    }

    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        return response()->json(['code' => 204]);
    }

    public function restore($user_id)
    {
        $user = User::withTrashed()->findOrFail($user_id);
        $user->restore();

        return response()->json(['user' => $user]);
    }

}
