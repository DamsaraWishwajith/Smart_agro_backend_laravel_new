<?php

namespace App\Filament\Resources\FarmConditionResource\Pages;

use App\Filament\Resources\FarmConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFarmConditions extends ListRecords
{
    protected static string $resource = FarmConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
