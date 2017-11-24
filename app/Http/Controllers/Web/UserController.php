<?php

namespace App\Http\Controllers\Web;

use App\Helper\ImageHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show($user_id)
    {
        $user = Auth::user();

        return response()->json(['user' => $user]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'pic'           =>  'image',
            'phone'         =>  'numeric',
            'qq'            =>  'numeric',
            'wechat'        =>  'string',
            'address'       =>  'string',
        ]);

        $file   = $request->file('pic');
        $path   = ImageHelper::saveImage($file);
        $user   = Auth::user();
        $user->update([
            'avatar_path'   =>  !empty($path) ? $path : $user->avatar_path,
            'phone'         =>  $request->input('phone'),
            'qq'            =>  $request->input('qq'),
            'wechat'        =>  $request->input('wechat'),
            'address'       =>  $request->input('address')
        ]);

        return response()->json(['user' => $user]);
    }

    public function signIn($user_id)
    {
        $user = Auth::user();
        if (empty($user->sign_in_time)) {
            $user->update([
                'sign_in_time'  =>  Carbon::now(),
                'integral'      =>  $user->integral + 1
            ]);
        } elseif (Carbon::now()->diffInDays(Carbon::parse($user->sign_in_time)) > 0) {
            $user->update([
                'sign_in_time'  =>  Carbon::now(),
                'integral'      =>  $user->integral + 1
            ]);
        }

        return response()->json(['user' => $user]);
    }
}
