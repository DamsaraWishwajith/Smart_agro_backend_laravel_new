<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FarmCondition;
use App\Models\User;
use App\Models\Motor;
use Illuminate\Support\Facades\Validator;

class FarmConditionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'temp' => 'required|numeric',
            'humidity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $condition = FarmCondition::create([
            'device_id' => $request->device_id,
            'temp' => $request->temp,
            'humidity' => $request->humidity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Farm conditions saved successfully',
            'data' => $condition
        ], 201);
    }

    public function getUserFarmData(Request $request)
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

        $user = User::find($request->user_id);

        if (!$user->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'This user does not have a device assigned.'
            ], 404);
        }

        // Get the latest condition for this device
        $latestCondition = FarmCondition::where('device_id', $user->device_id)
            ->latest()
            ->first();

        if (!$latestCondition) {
            return response()->json([
                'success' => false,
                'message' => 'No farm data found for this user\'s device.'
            ], 404);
        }

        // Get the user's plant settings (from our earlier plants table)
        $plant = $user->plants()->first();

        // Get the motor status for this user and device
        $motor = $user->motors()->where('device_id', $user->device_id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'device_id' => $user->device_id,
                'user_name' => $user->name,
                'temp' => $latestCondition->temp,
                'humidity' => $latestCondition->humidity,
                'recorded_at' => $latestCondition->created_at->toDateTimeString(),
                'plant' => $plant ? [
                    'crop_name' => $plant->crop_name,
                    'planted_days' => $plant->planted_days,
                    'temperature' => $plant->temperature,
                    'plants_count' => $plant->plants_count,
                    'water_time_seconds' => $plant->water_time_seconds,
                    'water_need_ml' => $plant->water_need_ml,
                    'updated_at' => $plant->updated_at->toDateTimeString(),
                ] : null,
                'motors' => $motor ? [
                    'drip' => $motor->drip,
                    'mist' => $motor->mist,
                    'exhaust' => $motor->exhaust,
                    'light' => $motor->light,
                    'updated_at' => $motor->updated_at->toDateTimeString(),
                ] : [
                    'drip' => 'OFF',
                    'mist' => 'OFF',
                    'exhaust' => 'OFF',
                    'light' => 'OFF',
                    'updated_at' => null,
                ]
            ]
        ]);
    }
}
