<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users
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
     * Get single user
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
     * Create user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'password' => 'required|string|min:6',
        ]);

        $user = Users::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'user_name' => $validated['user_name'],
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->user_name = $validated['user_name'];

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
            'message' => 'User archived successfully.',
            'data' => $user
        ]);
    }
}