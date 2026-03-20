<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();
        
        $validated = $request->validate([
            "username" => "sometimes|min:6|unique:users,username," . $user->id,
            "first_name" => "sometimes|string",
            "last_name" => "sometimes|string",
            "sex" => "sometimes|in:male,female",
            "date_of_birth" => "sometimes|date",

            "old_password" => "required_with:new_password",
            "new_password" => "nullable|min:8|confirmed"
        ]);

        $user->fill([
            "username" => $validated['username'] ?? $user->username,
            "first_name" => $validated['first_name'] ?? $user->first_name,
            "last_name" => $validated['last_name'] ?? $user->last_name,
            "sex" => $validated['sex'] ?? $user->sex,
            "date_of_birth" => $validated['date_of_birth'] ?? $user->date_of_birth
        ]);

        if (!empty($validated['new_password'])) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    "success" => false,
                    "message" => "Old password incorrect!"
                ], 401);
            }

            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Profile updated!",
            "data" => $user
        ]);
    }

    public function destroy(Request $request) {
        $request->user()->delete();

        return response()->json([
            'success' => true,
            'message' => "Account deleted successfully!"
        ]);
    }
}
