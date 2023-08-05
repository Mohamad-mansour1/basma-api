<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use Laravel\Passport\Passport;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function index()
    {
        // $user = new User();
        // $user->name = 'Basma';
        // $user->email = 'basma1@gmail.com';
        // $user->password = Hash::make('123456');
        // $user->save();

        return response()->json(['message' => 'You\'re already logged out!']);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');
    
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'These credentials do not match our records.'], 401);
        }
    
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return response()->json([
                                    'token' => $token,
                                    'message' => 'Logged in successfully!'
                                ]);
    }

    
    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Logged out successfully!']);
    }
    
}
