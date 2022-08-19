<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admins;

class Login extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function guard()
    {
        return Auth::guard();
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if ($token = $this->guard()->attempt($credentials)) {
    //         return $this->respondWithToken($token);
    //     }

    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }

    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => $this->guard()->factory()->getTTL() * 60,
    //         'user' => auth()->user()
    //     ]);
    // }

    public function registration(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'user_name' => 'required',
            'sex' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
            'status' => 'required',
        ]);

        $data = $request->all();
        return $this->create($data);
    }

    public function create(array $data)
    {
        return Admins::create([
            'first_name' => $data['first_name'],
            'user_name' => $data['user_name'],
            'sex' => $data['sex'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => $data['status'],
            'created_time' => current_time(),
            'created_date' => current_date(),
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return Redirect('login');
    }
}
