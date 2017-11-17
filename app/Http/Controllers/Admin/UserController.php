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

    public function avatar(Request $request)
    {
        $this->validate($request, [
            'pic'   =>  'required|image'
        ]);

        $file = $request->file('pic');
        if ($file->isValid()) {
            $path = ImageHelper::saveImage($file);
        }
        $user = Auth::user();
        $user->update([
            'avatar_path'   =>  !empty($path) ? $path : 1
        ]);

        return response()->json(['user' => $user]);
    }
}
