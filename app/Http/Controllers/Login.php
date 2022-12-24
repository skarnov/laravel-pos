<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Activities;
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

    public function login(Request $request)
    {


        // $activities = new Activities;

        // $activities->fk_admin_id = '1';
        // $activities->type = 'success';
        // $activities->name = 'Login Success';
        // $activities->ip_address = '192.178.01.02';
        // $activities->visitor_country = 'Bangladesh';
        // $activities->visitor_state = 'Khulna';
        // $activities->visitor_city = 'Magura';
        // $activities->visitor_address = 'Goal Bathan';
        // $activities->created_time = current_time();
        // $activities->created_date = current_date();
        // $activities->created_by = '1';

        // $activities->save();
        // $user_info = $this->me();


        // debug($user_info);

        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

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

    public function me()
    {
        return response()->json($this->guard()->user());
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }
}
