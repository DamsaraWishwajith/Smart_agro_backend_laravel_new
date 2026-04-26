<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mode;
use Illuminate\Support\Facades\Validator;

class ModeController extends Controller
{
    public function updateMode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'mode' => 'required|string', // e.g., 'AUTO', 'MANUAL'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update existing mode for the user or create a new one
        $mode = Mode::updateOrCreate(
            ['user_id' => $request->user_id],
            ['mode' => $request->mode]
        );

        return response()->json([
            'success' => true,
            'message' => 'Mode updated successfully',
            'data' => $mode
        ]);
    }

    public function getMode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mode = Mode::where('user_id', $request->user_id)->first();

        if (!$mode) {
            return response()->json([
                'success' => false,
                'message' => 'Mode not found for this user'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mode
        ]);
    }
}
