<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IrrigationSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessIrrigationSchedules extends Command
{
    protected $signature = 'irrigation:process';
    protected $description = 'Check irrigation schedules and trigger hardware if necessary';

    public function handle()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i'); // e.g., '06:00'

        // 1. Find all schedules that need to turn ON right now
        $turnOnSchedules = IrrigationSchedule::where('on_time', 'like', $currentTime . '%')->get();
        foreach ($turnOnSchedules as $schedule) {
            $this->triggerHardware($schedule->system_type, 'ON', $schedule->user_id);
        }

        // 2. Find all schedules that need to turn OFF right now
        $turnOffSchedules = IrrigationSchedule::where('off_time', 'like', $currentTime . '%')->get();
        foreach ($turnOffSchedules as $schedule) {
            $this->triggerHardware($schedule->system_type, 'OFF', $schedule->user_id);
        }

        $this->info("Irrigation schedules processed for $currentTime.");
    }

    private function triggerHardware($systemType, $action, $userId)
    {
        // TODO: Here you will write the code to communicate with your ESP32/Hardware via MQTT or API
        Log::info("Hardware Trigger: Turning $action the $systemType for User #$userId");
        $this->info("Triggered $systemType $action for User #$userId");
    }
}
