<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mode;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Schema;

class ModeController extends Controller
{
    private function ensureColumnExists()
    {
        if (!Schema::hasColumn('modes', 'mist_auto_schedule')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--path' => 'database/migrations/2026_07_11_140000_add_mist_auto_schedule_to_modes_table.php',
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                \Log::error('Migration failed in ModeController: ' . $e->getMessage());
            }
        }
    }

    public function updateMode(Request $request)
    {
        $this->ensureColumnExists();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'mode' => 'nullable|string', // e.g., 'AUTO', 'MANUAL'
            'mist_auto_schedule' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        if ($request->has('mode')) {
            $updateData['mode'] = $request->mode;
            // When switching to Manual mode, Auto Mist & Temp Control MUST be turned off
            if (strtoupper($request->mode) === 'MANUAL') {
                $updateData['mist_auto_schedule'] = false;

                // Turn OFF mist & exhaust motors that may have been triggered by Auto mode
                \App\Models\Motor::where('user_id', $request->user_id)->update([
                    'mist' => 'OFF',
                    'exhaust' => 'OFF',
                ]);
            }
        }
        if ($request->has('mist_auto_schedule')) {
            // Auto Mist & Temp Control can ONLY be enabled in AUTO mode
            $currentMode = $request->has('mode')
                ? strtoupper($request->mode)
                : strtoupper(Mode::where('user_id', $request->user_id)->value('mode') ?? 'MANUAL');

            if ($currentMode === 'MANUAL') {
                $updateData['mist_auto_schedule'] = false;
            } else {
                $updateData['mist_auto_schedule'] = (bool)$request->mist_auto_schedule;
            }
        }

        if (empty($updateData)) {
            return response()->json([
                'success' => false,
                'message' => 'No fields to update',
            ], 400);
        }

        // Update existing mode for the user or create a new one
        $mode = Mode::updateOrCreate(
            ['user_id' => $request->user_id],
            $updateData
        );

        return response()->json([
            'success' => true,
            'message' => 'Mode updated successfully',
            'data' => $mode
        ]);
    }

    public function getMode(Request $request)
    {
        $this->ensureColumnExists();

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

        // Auto Mist & Temp Control is only valid in AUTO mode. In MANUAL mode it is always false.
        if (strtoupper($mode->mode) === 'MANUAL' && $mode->mist_auto_schedule) {
            $mode->mist_auto_schedule = false;
            $mode->save();
        }

        $user = \App\Models\User::find($request->user_id);
        $is_online = false;
        
        if ($user && $user->device_id) {
            $latestCondition = \App\Models\FarmCondition::where('device_id', $user->device_id)
                ->latest()
                ->first();
            if ($latestCondition) {
                $lastSync = $latestCondition->updated_at;
                $secondsAgo = $lastSync ? $lastSync->diffInSeconds(now()) : 9999;
                $is_online = $secondsAgo <= 15;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $mode,
            'is_online' => $is_online
        ]);
    }
}
