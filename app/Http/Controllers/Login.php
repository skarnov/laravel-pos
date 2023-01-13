<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
        $validator = Validator::make(
            $request->all(),
            [
                'email'   => 'required|email',
                'password'   => 'required',
            ],
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'Form Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        } else {
            $credentials = $request->only('email', 'password');
            if ($token = $this->guard()->attempt($credentials)) {

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Login -' . auth()->user()->user_name;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                return $this->respondWithToken($token);
            }else{
                return response()->json([
                    'status' => 'error',
                    'msg'    => 'Credential Error',
                    'errors' => array('Invalid Credential!'),
                ], 422);
            }
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 6000000000000000000,
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
