<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FarmCondition;
use App\Models\User;
use App\Models\Motor;
use App\Models\EspNotification;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;

class FarmConditionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'temp' => 'required|numeric',
            'humidity' => 'required|numeric',
            'soil_moisture' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $condition = FarmCondition::where('device_id', $request->device_id)->first();

        if ($condition) {
            $condition->update([
                'temp' => $request->temp,
                'humidity' => $request->humidity,
                'soil_moisture' => $request->soil_moisture,
            ]);
        } else {
            $condition = FarmCondition::create([
                'device_id' => $request->device_id,
                'temp' => $request->temp,
                'humidity' => $request->humidity,
                'soil_moisture' => $request->soil_moisture,
            ]);
        }

        // Find the user associated with this device ID
        $user = User::where('device_id', $request->device_id)->first();
        if (!$user) {
            $user = User::first();
            if ($user) {
                $user->device_id = $request->device_id;
                $user->save();
            }
        }
        
        $modeStr = 'MANUAL';
        $motors = [
            'drip' => 'OFF',
            'mist' => 'OFF',
            'exhaust' => 'OFF',
            'light' => 'OFF',
        ];

        if ($user) {
            // Retrieve current system mode
            $modeModel = \App\Models\Mode::where('user_id', $user->id)->first();
            $modeStr = $modeModel ? $modeModel->mode : 'MANUAL';

            // Find or create motor status record
            $motorStatus = Motor::firstOrCreate(
                ['user_id' => $user->id, 'device_id' => $request->device_id],
                ['drip' => 'OFF', 'mist' => 'OFF', 'exhaust' => 'OFF', 'light' => 'OFF']
            );

            if ($modeStr === 'AUTO') {
                // AUTO Mode Logic
                // 1. Temperature-based controls
                $plant = $user->plants()->first();
                $targetTemp = $plant ? ($plant->temperature ?? 30) : 30;

                if ($request->temp >= $targetTemp) {
                    $motorStatus->exhaust = 'ON';
                    $motorStatus->mist = 'ON';
                } else {
                    $motorStatus->exhaust = 'OFF';
                    $motorStatus->mist = 'OFF';
                }

                // 2. Schedule-based controls using Colombo timezone
                $now = \Carbon\Carbon::now('Asia/Colombo');
                $currentDay = $now->format('D'); // e.g. "Mon"
                $currentTime = $now->format('H:i:s'); // e.g. "14:30:00"

                // Check drip irrigation schedules
                $dripSchedules = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                    ->where('system_type', 'drip')
                    ->get();
                $isDripActive = false;
                foreach ($dripSchedules as $schedule) {
                    $days = $schedule->days;
                    if ($days && is_array($days) && in_array($currentDay, $days)) {
                        if ($currentTime >= $schedule->on_time && $currentTime <= $schedule->off_time) {
                            $isDripActive = true;
                            break;
                        }
                    }
                }
                $motorStatus->drip = $isDripActive ? 'ON' : 'OFF';

                // Check light system schedules
                $lightSchedules = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                    ->where('system_type', 'light_time')
                    ->get();
                $isLightActive = false;
                foreach ($lightSchedules as $schedule) {
                    $days = $schedule->days;
                    if ($days && is_array($days) && in_array($currentDay, $days)) {
                        if ($currentTime >= $schedule->on_time && $currentTime <= $schedule->off_time) {
                            $isLightActive = true;
                            break;
                        }
                    }
                }
                $motorStatus->light = $isLightActive ? 'ON' : 'OFF';

                $motorStatus->save();
            }

            $motors = [
                'drip' => $motorStatus->drip,
                'mist' => $motorStatus->mist,
                'exhaust' => $motorStatus->exhaust,
                'light' => $motorStatus->light,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Farm conditions saved successfully',
            'data' => $condition,
            'mode' => $modeStr,
            'motors' => $motors
        ], 200);
    }

    public function getUserFarmData(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate');
            
            // Seed sample device events if empty
            if (\App\Models\DeviceEvent::count() == 0) {
                $user = User::first();
                if ($user && $user->device_id) {
                    $baseTime = now()->subDays(3);
                    for ($i = 0; $i < 4; $i++) {
                        $cut = (clone $baseTime)->addHours($i * 18 + rand(1, 5));
                        $restore = (clone $cut)->addMinutes(rand(12, 58));
                        
                        \App\Models\DeviceEvent::create([
                            'device_id'   => $user->device_id,
                            'user_id'     => $user->id,
                            'event_type'  => 'power_cut',
                            'occurred_at' => $cut,
                        ]);
                        
                        \App\Models\DeviceEvent::create([
                            'device_id'   => $user->device_id,
                            'user_id'     => $user->id,
                            'event_type'  => 'power_restored',
                            'occurred_at' => $restore,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Migration or Seeding failed: " . $e->getMessage());
        }

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

        // Determine online status: online if last sync was within 15 seconds
        $lastSync   = $latestCondition->updated_at;
        $secondsAgo = $lastSync ? $lastSync->diffInSeconds(now()) : 9999;
        $isOnline   = $secondsAgo <= 15;

        return response()->json([
            'success' => true,
            'data' => [
                'device_id'    => $user->device_id,
                'user_name'    => $user->name,
                'is_online'    => $isOnline,
                'last_seen_at' => $lastSync ? $lastSync->toDateTimeString() : null,
                'seconds_since_sync' => $secondsAgo,
                'temp'         => $latestCondition->temp,
                'humidity'     => $latestCondition->humidity,
                'soil_moisture'=> $latestCondition->soil_moisture ?? null,
                'recorded_at'  => $latestCondition->created_at->toDateTimeString(),
                'plant' => $plant ? [
                    'crop_name'          => $plant->crop_name,
                    'planted_days'       => $plant->planted_days,
                    'temperature'        => $plant->temperature,
                    'plants_count'       => $plant->plants_count,
                    'water_time_seconds' => $plant->water_time_seconds,
                    'water_need_ml'      => $plant->water_need_ml,
                    'updated_at'         => $plant->updated_at->toDateTimeString(),
                ] : null,
                'motors' => $motor ? [
                    'drip'       => $motor->drip,
                    'mist'       => $motor->mist,
                    'exhaust'    => $motor->exhaust,
                    'light'      => $motor->light,
                    'updated_at' => $motor->updated_at->toDateTimeString(),
                ] : [
                    'drip'       => 'OFF',
                    'mist'       => 'OFF',
                    'exhaust'    => 'OFF',
                    'light'      => 'OFF',
                    'updated_at' => null,
                ]
            ]
        ]);
    }

    public function esp32Sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'temp' => 'required|numeric',
            'humidity' => 'required|numeric',
            'soil_moisture' => 'nullable|numeric',
            'increment_day' => 'nullable|integer',
            'notify_msg' => 'nullable|string',
            'notify_drip_msg' => 'nullable|string',
            'boot' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $condition = FarmCondition::where('device_id', $request->device_id)->first();

        // Find associated user
        $user = User::where('device_id', $request->device_id)->first();
        if (!$user) {
            $user = User::first();
            if ($user) {
                $user->device_id = $request->device_id;
                $user->save();
            }
        }

        // If ESP32 is reporting a boot (startup), calculate power cut/restored times
        if ($request->input('boot') == 1 && $condition) {
            $lastSeen = $condition->updated_at;
            $secondsAgo = $lastSeen ? $lastSeen->diffInSeconds(now()) : 9999;
            if ($secondsAgo > 10) {
                // Log power cut event (at the last seen timestamp)
                \App\Models\DeviceEvent::create([
                    'device_id'   => $request->device_id,
                    'user_id'     => $user ? $user->id : null,
                    'event_type'  => 'power_cut',
                    'occurred_at' => $lastSeen,
                ]);

                // Log power restored event (at the current timestamp)
                \App\Models\DeviceEvent::create([
                    'device_id'   => $request->device_id,
                    'user_id'     => $user ? $user->id : null,
                    'event_type'  => 'power_restored',
                    'occurred_at' => now(),
                ]);

                \Log::info("[ESP32 Sync] Power cut & restoration logged. Duration: {$secondsAgo}s");
            }
        }

        if ($condition) {
            $condition->update([
                'temp' => $request->temp,
                'humidity' => $request->humidity,
                'soil_moisture' => $request->soil_moisture,
            ]);
        } else {
            $condition = FarmCondition::create([
                'device_id' => $request->device_id,
                'temp' => $request->temp,
                'humidity' => $request->humidity,
                'soil_moisture' => $request->soil_moisture,
            ]);
        }
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Device not assigned to any user and no users exist in the system.'
            ], 404);
        }

        // Handle day increment
        if ($request->input('increment_day') == 1) {
            $plant = $user->plants()->first();
            if ($plant) {
                $plant->increment('planted_days');
            }
        }

        // Handle notifications (save to esp_notifications and send real push notifications)
        // Deduplicate notifications to prevent spamming when ESP32 retries due to network timeouts
        if ($request->filled('notify_msg')) {
            $duplicate = EspNotification::where('device_id', $request->device_id)
                ->where('message', $request->notify_msg)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->exists();

            if (!$duplicate) {
                EspNotification::create([
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                    'title' => 'Greenhouse Alert',
                    'message' => $request->notify_msg,
                    'is_read' => false,
                ]);
                $this->sendPushNotification($user, 'Greenhouse Alert', $request->notify_msg);
            }
        }
        if ($request->filled('notify_drip_msg')) {
            $duplicate = EspNotification::where('device_id', $request->device_id)
                ->where('message', $request->notify_drip_msg)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->exists();

            if (!$duplicate) {
                EspNotification::create([
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                    'title' => 'Drip Irrigation Alert',
                    'message' => $request->notify_drip_msg,
                    'is_read' => false,
                ]);
                $this->sendPushNotification($user, 'Drip Irrigation Alert', $request->notify_drip_msg);
            }
        }

        // Get plant settings
        $plant = $user->plants()->first();
        $cropName = $plant ? $plant->crop_name : 'Manual';
        
        // Map crop_name to selection index
        // 1 = Tomato, 2 = Cucumber, 3 = Bell Pepper, 4 = Fish Chili, 5 = Nai Chili, 6 = Chili, 7 = Manual
        $selection = 7;
        if ($cropName) {
            $normalizedName = strtolower(trim($cropName));
            if ($normalizedName === 'tomato') $selection = 1;
            else if ($normalizedName === 'cucumber') $selection = 2;
            else if ($normalizedName === 'bell pepper') $selection = 3;
            else if ($normalizedName === 'fish chili' || $normalizedName === 'scotch bonnet' || $normalizedName === 'scotch') $selection = 4;
            else if ($normalizedName === 'capsicum' || $normalizedName === 'nai chili') $selection = 5;
            else if ($normalizedName === 'chilli' || $normalizedName === 'chili') $selection = 6;
        }

        // Get Mode
        $modeModel = \App\Models\Mode::where('user_id', $user->id)->first();
        $modeStr = ($modeModel && strtolower($modeModel->mode) === 'auto') ? 'Auto' : 'Manual';

        // Get or create Motor status
        $motorStatus = Motor::firstOrCreate(
            ['user_id' => $user->id, 'device_id' => $request->device_id],
            ['drip' => 'OFF', 'mist' => 'OFF', 'exhaust' => 'OFF', 'light' => 'OFF']
        );

        // Process User-Defined Schedules with Edge-Triggering (Cache) ONLY in Manual Mode
        if ($modeStr === 'Manual') {
            $now = \Carbon\Carbon::now('Asia/Colombo');
            $currentDay = $now->format('D'); // e.g. "Mon"
            $currentTime = $now->format('H:i:s'); // e.g. "18:30:00"

            \Log::debug("[ESP32 Sync] Manual Mode | Day={$currentDay} | Time={$currentTime} | Device={$request->device_id}");

            // 1. Drip Irrigation Schedules
            $dripSchedules = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                ->where('system_type', 'drip')
                ->get();
            $isDripScheduled = false;
            foreach ($dripSchedules as $schedule) {
                $days = $schedule->days;
                $scheduleDays = is_array($days) ? $days : json_decode($days, true);
                if ($scheduleDays && in_array($currentDay, $scheduleDays)) {
                    if ($currentTime >= $schedule->on_time && $currentTime <= $schedule->off_time) {
                        $isDripScheduled = true;
                        break;
                    }
                }
            }
            
            $expectedDrip = $isDripScheduled ? 'ON' : 'OFF';
            $prevDrip = \Illuminate\Support\Facades\Cache::get("drip_sched_{$user->id}_{$request->device_id}", 'OFF');
            if ($expectedDrip !== $prevDrip) {
                $motorStatus->drip = $expectedDrip;
                \Illuminate\Support\Facades\Cache::put("drip_sched_{$user->id}_{$request->device_id}", $expectedDrip);
                \Log::debug("[Edge Trigger] Drip changed to {$expectedDrip}");
            }

            // 2. Light System Schedules
            $lightSchedules = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                ->where('system_type', 'light')
                ->get();
            $isLightScheduled = false;
            foreach ($lightSchedules as $schedule) {
                $days = $schedule->days;
                $scheduleDays = is_array($days) ? $days : json_decode($days, true);
                if ($scheduleDays && in_array($currentDay, $scheduleDays)) {
                    if ($currentTime >= $schedule->on_time && $currentTime <= $schedule->off_time) {
                        $isLightScheduled = true;
                        break;
                    }
                }
            }
            
            $expectedLight = $isLightScheduled ? 'ON' : 'OFF';
            $prevLight = \Illuminate\Support\Facades\Cache::get("light_sched_{$user->id}_{$request->device_id}", 'OFF');
            if ($expectedLight !== $prevLight) {
                $motorStatus->light = $expectedLight;
                \Illuminate\Support\Facades\Cache::put("light_sched_{$user->id}_{$request->device_id}", $expectedLight);
                \Log::debug("[Edge Trigger] Light changed to {$expectedLight}");
            }

            // 3. Mist & Exhaust control
            $mistSchedulesCount = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                ->where('system_type', 'mist')
                ->count();
            $mistTimeVal = $mistSchedulesCount > 0 ? 1 : 0;

            if ($mistTimeVal === 0) {
                // Sensor-threshold mode for Mist (Edge-triggered to allow manual overrides)
                $targetTemp = $plant ? ($plant->temperature ?? 30) : 30;
                $expectedMist = 'OFF';
                if ($request->temp >= $targetTemp) {
                    $expectedMist = 'ON';
                } else if ($request->temp < ($targetTemp - 5)) {
                    $expectedMist = 'OFF';
                } else {
                    $expectedMist = \Illuminate\Support\Facades\Cache::get("mist_sched_{$user->id}_{$request->device_id}", 'OFF');
                }
                
                $prevMist = \Illuminate\Support\Facades\Cache::get("mist_sched_{$user->id}_{$request->device_id}", 'OFF');
                if ($expectedMist !== $prevMist) {
                    $motorStatus->mist = $expectedMist;
                    $motorStatus->exhaust = $expectedMist;
                    \Illuminate\Support\Facades\Cache::put("mist_sched_{$user->id}_{$request->device_id}", $expectedMist);
                }
            } else {
                // Timer mode
                $mistSchedules = \App\Models\IrrigationSchedule::where('user_id', $user->id)
                    ->where('system_type', 'mist')
                    ->get();
                $isMistScheduled = false;
                foreach ($mistSchedules as $schedule) {
                    $days = $schedule->days;
                    $scheduleDays = is_array($days) ? $days : json_decode($days, true);
                    if ($scheduleDays && in_array($currentDay, $scheduleDays)) {
                        if ($currentTime >= $schedule->on_time && $currentTime <= $schedule->off_time) {
                            $isMistScheduled = true;
                            break;
                        }
                    }
                }
                
                $expectedMist = $isMistScheduled ? 'ON' : 'OFF';
                $prevMist = \Illuminate\Support\Facades\Cache::get("mist_sched_{$user->id}_{$request->device_id}", 'OFF');
                if ($expectedMist !== $prevMist) {
                    $motorStatus->mist = $expectedMist;
                    $motorStatus->exhaust = $expectedMist;
                    \Illuminate\Support\Facades\Cache::put("mist_sched_{$user->id}_{$request->device_id}", $expectedMist);
                }
            }

            $motorStatus->save();
        } else {
            // Auto Mode: Clear any residual manual motor states so ESP32 can run its built-in logic properly.
            // If we don't clear this, a previously forced 'ON' state from manual mode would permanently override the ESP32's internal timers.
            if ($motorStatus->drip !== 'OFF' || $motorStatus->mist !== 'OFF' || $motorStatus->exhaust !== 'OFF' || $motorStatus->light !== 'OFF') {
                $motorStatus->drip = 'OFF';
                $motorStatus->mist = 'OFF';
                $motorStatus->exhaust = 'OFF';
                $motorStatus->light = 'OFF';
                $motorStatus->save();
            }
        }

        \Log::debug("[ESP32 Sync] Result → drip={$motorStatus->drip} mist={$motorStatus->mist} light={$motorStatus->light}");


        // Get mist timer vs threshold setting (mist_time_val) from user's Mode table (mist_auto_schedule)
        $userMode = \App\Models\Mode::where('user_id', $user->id)->first();
        $mistTimeVal = ($userMode && $userMode->mist_auto_schedule) ? 1 : 0;

        return response()->json([
            'success' => true,
            'mode' => $modeStr,
            'selection' => $selection,
            'days' => $plant ? ($plant->planted_days ?? 0) : 0,
            'plants' => $plant ? ($plant->plants_count ?? 1) : 1,
            'plant1_water_ml' => $plant ? ($plant->water_need_ml ?? 250) : 250,
            'plant1_100ml_time' => $plant ? ($plant->water_time_seconds ?? 360) : 360,
            'mtemp' => $plant ? (int)($plant->temperature ?? 30) : 30,
            'mist_time_val' => $mistTimeVal,
            'drip' => strtolower($motorStatus->drip),
            'mist' => strtolower($motorStatus->mist),
            'exhust' => strtolower($motorStatus->exhaust),
            'light' => strtolower($motorStatus->light),
        ]);
    }

    /**
     * Send real-time FCM Push Notification helper.
     */
    private function sendPushNotification($user, $title, $body)
    {
        if (!$user->fcm_token) {
            return;
        }

        try {
            $options = \Kreait\Firebase\Http\HttpClientOptions::default()
                ->withGuzzleConfigOptions(['verify' => storage_path('app/cacert.pem')]);

            $factory = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(base_path(config('firebase.projects.app.credentials')))
                ->withHttpClientOptions($options);

            $messaging = $factory->createMessaging();
            $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);

            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($notification);

            $messaging->send($message);
        } catch (\Exception $e) {
            \Log::error('FCM Push Notification failed: ' . $e->getMessage());
        }
    }

    public function getDeviceEvents(Request $request)
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
                'message' => 'No device registered for this user.',
                'data' => []
            ]);
        }

        $events = \App\Models\DeviceEvent::where('device_id', $user->device_id)
            ->orderBy('occurred_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }
}
