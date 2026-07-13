<?php

namespace App\Filament\Resources\IrrigationScheduleResource\Pages;

use App\Filament\Resources\IrrigationScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIrrigationSchedule extends EditRecord
{
    protected static string $resource = IrrigationScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
