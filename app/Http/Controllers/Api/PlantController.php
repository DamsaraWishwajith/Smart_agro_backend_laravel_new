<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Plant;
use App\Models\User;
use App\Models\IrrigationSchedule;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|numeric|exists:users,id',
            'crop_name' => 'nullable|string|max:255',
            'planted_days' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'plants_count' => 'nullable|numeric',
            'water_time_seconds' => 'nullable|numeric',
            'water_need_ml' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine which user to associate the plant with
        $user = null;

        if ($request->has('user_id')) {
            $user = User::find($request->user_id);
        }

        if (!$user) {
            $user = $request->user() ?? User::first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No valid user found to associate with this plant.'
            ], 400);
        }

        // Create or Update the plant record associated with the user
        $plant = Plant::updateOrCreate(
            ['user_id' => $user->id],
            [
                'crop_name' => $request->crop_name,
                'planted_days' => $request->planted_days,
                'temperature' => $request->temperature,
                'plants_count' => $request->plants_count,
                'water_time_seconds' => $request->water_time_seconds,
                'water_need_ml' => $request->water_need_ml,
            ]
        );

        $status = $plant->wasRecentlyCreated ? 201 : 200;
        $message = $plant->wasRecentlyCreated ? 'Plant information saved successfully' : 'Plant information updated successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $plant
        ], $status);
    }

    public function saveSchedules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'system_type' => 'required|string',
            'slots' => 'required|array',
            'slots.*.on_time' => 'required|string', // HH:mm format
            'slots.*.off_time' => 'required|string', // HH:mm format
            'days' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old schedules for this system before saving new ones
        IrrigationSchedule::where('user_id', $request->user_id)
            ->where('system_type', $request->system_type)
            ->delete();

        // Save each new slot
        $savedSlots = [];
        foreach ($request->slots as $slot) {
            $savedSlots[] = IrrigationSchedule::create([
                'user_id' => $request->user_id,
                'system_type' => $request->system_type,
                'days' => $slot['days'] ?? $request->input('days'),
                'on_time' => $slot['on_time'],
                'off_time' => $slot['off_time'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Schedules updated successfully',
            'data' => $savedSlots
        ]);
    }

    public function getSchedules(Request $request)
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

        $schedules = IrrigationSchedule::where('user_id', $request->user_id)->get();

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function deleteSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|numeric|exists:irrigation_schedules,id',
            'user_id'     => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $schedule = IrrigationSchedule::where('id', $request->schedule_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found or access denied.'
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.'
        ]);
    }

    public function updateSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|numeric|exists:irrigation_schedules,id',
            'user_id'     => 'required|numeric|exists:users,id',
            'on_time'     => 'required|string',
            'off_time'    => 'required|string',
            'days'        => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $schedule = IrrigationSchedule::where('id', $request->schedule_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found or access denied.'
            ], 404);
        }

        $schedule->update([
            'on_time'  => $request->on_time,
            'off_time' => $request->off_time,
            'days'     => $request->days,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'data'    => $schedule->fresh()
        ]);
    }

    public function getPlantByDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('device_id', $request->device_id)->first();
        if (!$user) {
            $user = User::first();
            if ($user) {
                $user->device_id = $request->device_id;
                $user->save();
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found for this device ID and no users exist.'
            ], 404);
        }

        $plant = $user->plants()->first();

        if (!$plant) {
            return response()->json([
                'success' => false,
                'message' => 'No plant details found for this device.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plant
        ]);
    }
}

