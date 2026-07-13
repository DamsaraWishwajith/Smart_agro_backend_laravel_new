<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FarmCondition;
use App\Models\User;

class SimulateFarmData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'farm:simulate {device_id=001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate real-time temperature and humidity data for a device';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deviceId = $this->argument('device_id');
        $this->info("Starting real-time simulation for Device ID: $deviceId");
        $this->info("Press Ctrl+C to stop simulation.\n");

        // Find if any user is associated with this device
        $user = User::where('device_id', $deviceId)->first();
        if ($user) {
            $this->line("Device belongs to user: <info>{$user->name}</info> ({$user->email})");
        } else {
            $this->warn("Warning: No user is currently assigned to Device ID: $deviceId");
        }

        // Initial values (around comfortable tropical/greenhouse levels)
        $temp = 28.0;
        $humidity = 70.0;

        while (true) {
            // Add slight random fluctuation
            $temp += (rand(-5, 5) / 10.0);
            $humidity += (rand(-10, 10) / 10.0);

            // Clamp values to realistic ranges
            $temp = max(15.0, min(45.0, $temp));
            $humidity = max(30.0, min(100.0, $humidity));

            // Save to database
            FarmCondition::create([
                'device_id' => $deviceId,
                'temp' => round($temp, 1),
                'humidity' => round($humidity, 1),
            ]);

            $this->line("[" . date('Y-m-d H:i:s') . "] Temp: " . round($temp, 1) . "°C | Humidity: " . round($humidity, 1) . "% (Saved to DB)");

            sleep(2);
        }
    }
}
