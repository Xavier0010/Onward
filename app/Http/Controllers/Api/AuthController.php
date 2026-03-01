<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request) {
                
        $validated = $request->validate([
            'username' => 'required|unique:users|min:6',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required',
            'last_name' => 'required',
            'sex' => 'required|in:male,female',
            'date_of_birth' => 'required|date'
        ]);

        $user = User::create($validated);
        Auth::login($user);

        return response()->json([
            'message' => 'Akun berhasil terdaftar!'
        ]);
    }

    public function login(Request $request) {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginType, $request->login)->first();

        if(!$user) {
            return response()->json([
                'message' => ucfirst($loginType) . ' not registered!'
            ], 404);
        }

        if(!Auth::attempt([
            $loginType => $request->login,
            'password' => $request->password
        ])) {
            return response()->json([
                'message' => 'Password incorrect!'
            ], 401);
        }

        // $request->session()->regenerate();

        return response()->json([
            'message' => 'Login success!'
        ]);
    }
}
