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
}
