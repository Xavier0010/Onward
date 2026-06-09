<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {
                
        $validator = validator($request->all(), [
            "username" => "required|min:6",
            "email" => "required|email",
            "password" => "required|min:8|confirmed",
            "first_name" => "required",
            "last_name" => "required",
            "gender" => "required|in:male,female",
            "date_of_birth" => "required|date",
            "nationality" => "nullable|string|max:100"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Validation failed!",
                "errors" => $validator->errors()
            ], 400);
        }

        $userExists = User::where('email', $request->email)->orWhere('username', $request->username)->exists();

        if ($userExists) {
            return response()->json([
                "success" => false,
                "message" => "User already exist!"
            ], 409);
        }

        $validated = $validator->validated();

        $validated["password"] = Hash::make($validated["password"]);

        $user = User::create($validated);
        $token = $user->createToken("api_token")->plainTextToken;

        $achievementService = new AchievementService;
        $achievementService->check($user, 'registered');

        return response()->json([
            "success" => true,
            "message" => "Account registered!",
            "data" => [
                "user" => $user,
                "token" => $token
            ]
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            "login" => "required",
            "password" => "required"
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? "email" : "username";

        $user = User::where($loginType, $request->login)->first();

        if(!$user) {
            return response()->json([
                "message" => ucfirst($loginType) . " not registered!"
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)) {
            return response()->json([
                "success" => false,
                "message" => "Credentials incorrect!"
            ], 401);
        }

        $token = $user->createToken("api_token")->plainTextToken;
        $role = $user->role;

        return response()->json([
            "success" => true,
            "message" => "Login successful!",
            "data" => [
                "user" => $user,
                "token" => $token,
                "role" => $role
            ]
        ]);
    }
}
