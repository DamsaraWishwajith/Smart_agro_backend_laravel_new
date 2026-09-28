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

        // Verify if physical IoT device is currently online
        $condition = \App\Models\FarmCondition::where('device_id', $request->device_id)->latest('updated_at')->first();
        $isOnline = $condition && $condition->updated_at && $condition->updated_at->diffInSeconds(now()) <= 15;

        // If trying to turn ON any actuator while device is offline, block and reject
        $isTurningOn = ($request->drip === 'ON') || ($request->mist === 'ON') || ($request->exhaust === 'ON') || ($request->light === 'ON');
        if (!$isOnline && $isTurningOn) {
            return response()->json([
                'success' => false,
                'message' => 'Device is offline. Please make sure the ESP32 system is powered on before turning on components.',
                'is_online' => false,
            ], 400);
        }

        // Find or create the motor record for this physical device
        $motor = Motor::where('device_id', $request->device_id)->first();
        if (!$motor) {
            $motor = new Motor([
                'device_id' => $request->device_id,
                'user_id' => $request->user_id,
            ]);
        } else {
            $motor->user_id = $request->user_id;
        }

        $oldDrip = $motor->drip;
        $oldMist = $motor->mist;
        $oldExhaust = $motor->exhaust;
        $oldLight = $motor->light;

        // Update only the fields that are present in the request
        if ($request->has('drip')) $motor->drip = $request->drip;
        if ($request->has('mist')) $motor->mist = $request->mist;
        if ($request->has('exhaust')) $motor->exhaust = $request->exhaust;
        if ($request->has('light')) $motor->light = $request->light;

        $motor->save();

        // Send instant push notifications for manual actuator actions (only when online)
        $user = \App\Models\User::find($request->user_id);
        if ($user && $isOnline) {
            $device = $request->device_id;
            if ($request->has('drip') && $oldDrip !== $request->drip) {
                $status = $request->drip === 'ON' ? 'ON' : 'OFF';
                $title = "Drip Irrigation Alert";
                $body = "Drip Irrigation Pump was manually turned {$status}.";
                \App\Models\EspNotification::create(['user_id' => $user->id, 'device_id' => $device, 'title' => $title, 'message' => $body, 'is_read' => false]);
                $user->sendPushNotification($title, $body);
            }
            if ($request->has('mist') && $oldMist !== $request->mist) {
                $status = $request->mist === 'ON' ? 'ON' : 'OFF';
                $title = "Mist Irrigation Alert";
                $body = "Mist Spray Pump was manually turned {$status}.";
                \App\Models\EspNotification::create(['user_id' => $user->id, 'device_id' => $device, 'title' => $title, 'message' => $body, 'is_read' => false]);
                $user->sendPushNotification($title, $body);
            }
            if ($request->has('exhaust') && $oldExhaust !== $request->exhaust) {
                $status = $request->exhaust === 'ON' ? 'ON' : 'OFF';
                $title = "Exhaust Fan Alert";
                $body = "Exhaust Ventilation Fan was manually turned {$status}.";
                \App\Models\EspNotification::create(['user_id' => $user->id, 'device_id' => $device, 'title' => $title, 'message' => $body, 'is_read' => false]);
                $user->sendPushNotification($title, $body);
            }
            if ($request->has('light') && $oldLight !== $request->light) {
                $status = $request->light === 'ON' ? 'ON' : 'OFF';
                $title = "Grow Light Alert";
                $body = "Grow Light was manually turned {$status}.";
                \App\Models\EspNotification::create(['user_id' => $user->id, 'device_id' => $device, 'title' => $title, 'message' => $body, 'is_read' => false]);
                $user->sendPushNotification($title, $body);
            }
        }

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

        $user = \App\Models\User::find($request->user_id);
        $deviceId = $request->device_id ?? ($user ? $user->device_id : null);

        $motor = Motor::where('device_id', $deviceId)->latest('updated_at')->first()
              ?? Motor::where('user_id', $request->user_id)->latest('updated_at')->first();

        if (!$motor) {
            return response()->json([
                'success' => false,
                'message' => 'Motor data not found'
            ], 404);
        }

        $condition = $deviceId ? \App\Models\FarmCondition::where('device_id', $deviceId)->latest('updated_at')->first() : null;
        $isOnline = $condition && $condition->updated_at && $condition->updated_at->diffInSeconds(now()) <= 15;

        $data = $motor->toArray();
        if (!$isOnline) {
            $data['drip'] = 'OFF';
            $data['mist'] = 'OFF';
            $data['exhaust'] = 'OFF';
            $data['light'] = 'OFF';
        }

        return response()->json([
            'success' => true,
            'is_online' => (bool)$isOnline,
            'data' => $data
        ]);
    }
}
