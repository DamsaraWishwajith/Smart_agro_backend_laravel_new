<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * Get the status of a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Status retrieved successfully',
            'status' => $user->status,
        ]);
    }
    /**
     * Get a user and all their data by email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserByEmail(Request $request)
    {
        $request->validate([
            'user_email' => 'required|email',
        ]);

        $user = User::with([
                'plants',
                'motors',
                'irrigationSchedules',
                'messages',
            ])
            ->where('email', $request->user_email)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with that email address.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User data retrieved successfully.',
            'data' => $user,
        ]);
    }
}
