<?php

namespace App\Filament\Resources\FarmConditionResource\Pages;

use App\Filament\Resources\FarmConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarmCondition extends EditRecord
{
    protected static string $resource = FarmConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
