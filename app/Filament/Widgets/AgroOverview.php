<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgroOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', \App\Models\User::count())
                ->description('Registered farm owners')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Active Crops', \App\Models\Plant::count())
                ->description('Total planted crops across all farms')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),
            Stat::make('Average Temp', round(\App\Models\FarmCondition::avg('temp'), 1) . '°C')
                ->description('Mean temperature across devices')
                ->descriptionIcon('heroicon-m-sun')
                ->color('warning'),
            Stat::make('Average Soil Moisture', round(\App\Models\FarmCondition::avg('soil_moisture') ?? 0, 1) . '%')
                ->description('Mean soil moisture across devices')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('success'),
        ];
    }
}
