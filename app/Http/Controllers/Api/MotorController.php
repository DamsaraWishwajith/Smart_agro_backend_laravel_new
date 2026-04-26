<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;
use Illuminate\Support\Facades\Validator;

class MotorController extends Controller
{
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'device_id' => 'required|string',
            'drip' => 'nullable|string|in:ON,OFF',
            'mist' => 'nullable|string|in:ON,OFF',
            'exhaust' => 'nullable|string|in:ON,OFF',
            'light' => 'nullable|string|in:ON,OFF',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find or create the motor record for this user and device
        $motor = Motor::firstOrNew([
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
        ]);

        // Update only the fields that are present in the request
        if ($request->has('drip')) $motor->drip = $request->drip;
        if ($request->has('mist')) $motor->mist = $request->mist;
        if ($request->has('exhaust')) $motor->exhaust = $request->exhaust;
        if ($request->has('light')) $motor->light = $request->light;

        $motor->save();

        return response()->json([
            'success' => true,
            'message' => 'Motor status updated successfully',
            'data' => $motor
        ]);
    }

    public function getStatus(Request $request)
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

        $motor = Motor::where('user_id', $request->user_id)->first();

        if (!$motor) {
            return response()->json([
                'success' => false,
                'message' => 'Motor data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $motor
        ]);
    }
}
