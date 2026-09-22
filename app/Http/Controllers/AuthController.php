<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = Users::where('email', $validated['email'])
            ->where('is_archived', 0)
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'id' => $user->id,
                'store_name' => $user->store_name,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
            ]
        ]);
    }
}