<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TemperatureTrend extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = \App\Models\FarmCondition::latest()->take(10)->get()->reverse();

        return [
            'datasets' => [
                [
                    'label' => 'Temperature (°C)',
                    'data' => $data->pluck('temp')->toArray(),
                    'borderColor' => '#fbbf24',
                ],
                [
                    'label' => 'Humidity (%)',
                    'data' => $data->pluck('humidity')->toArray(),
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Soil Moisture (%)',
                    'data' => $data->pluck('soil_moisture')->toArray(),
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $data->pluck('created_at')->map(fn ($date) => $date->format('H:i'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
