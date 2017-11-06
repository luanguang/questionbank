<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Validator, Input, Redirect;

class AuthenticateController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password');
        $user        = User::where('name', $credentials['name'])->first();

        $validator   = Validator::make($credentials, [
            'name'      => 'required|string',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'code'      => 1,
                    'message'   => 'Validation failed.',
                    'errors'    => $validator->errors()
                ], 422);
        }

        $token = JWTAuth::attempt($credentials);

        if ($token) {
            return response()->json(['token' => $token, 'user'  =>  $user]);
        } else {
            return response()->json(['code' => 2, 'message' => 'Invalid credentials.'], 401);
        }
    }

    /**
     * Get the user by token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        JWTAuth::setToken($request->input('token'));
        $user = JWTAuth::toUser();
        return response()->json($user);
    }

    public function register(Request $request)
    {
        $rules = [
            'name'          => 'required|max:255',
            'password'      => 'required|confirmed|min:6',
            'profession'    => 'required|string|in:teacher,student'
        ];
        $input = $request->only('name', 'profession', 'password', 'password_confirmation');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $error = $validator->messages()->tojson();
            return response()->json(['success'  =>  false, 'error' => $error]);
        }

        User::create([
            'name'          =>  $request->input('name'),
            'profession'    =>  $request->input('profession'),
            'password'      =>  bcrypt($request->input('password'))
        ]);

        return response()->json(['success'=> true]);
    }

    public function logout()
    {
        $this->logout();
        JWTAuth::invalidate(JWTAuth::getToken());

        return;
    }
}
