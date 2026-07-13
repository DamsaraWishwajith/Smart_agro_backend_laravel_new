<?php

namespace App\Filament\Resources\IrrigationScheduleResource\Pages;

use App\Filament\Resources\IrrigationScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIrrigationSchedules extends ListRecords
{
    protected static string $resource = IrrigationScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
