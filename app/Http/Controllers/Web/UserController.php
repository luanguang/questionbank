<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return $user->toJson();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'string',
        ]);

        $user = Auth::user();
        $user->update($request, [
            'name'  =>  $request->input('name')
        ]);

        return $user->toJson();
    }
}
