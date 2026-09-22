<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    /**
     * Get all active users
     */
    public function index()
    {
        $users = Users::where('is_archived', 0)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get a single user
     */
    public function show($id)
    {
        $user = Users::where('id', $id)
            ->where('is_archived', 0)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',

            'name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email',

            'phone_number' => [
                'required',
                'regex:/^(09\d{9}|\+639\d{9})$/'
            ],

            'password' => 'required|string|min:6|confirmed',
        ], [
            'store_name.required' => 'Store name is required.',

            'name.required' => 'Your name is required.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',

            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => 'Phone number must start with 09 or +639 and contain 11 digits.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $user = Users::create([
            'store_name' => $validated['store_name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'is_archived' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = Users::where('id', $id)
            ->where('is_archived', 0)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',

            'name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $id,

            'phone_number' => [
                'required',
                'regex:/^(09\d{9}|\+639\d{9})$/'
            ],

            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->store_name = $validated['store_name'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }

    /**
     * Archive user
     */
    public function archive($id)
    {
        $user = Users::where('id', $id)
            ->where('is_archived', 0)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $user->is_archived = 1;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User archived successfully.'
        ]);
    }
}