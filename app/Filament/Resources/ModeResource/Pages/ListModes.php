<?php

namespace App\Filament\Resources\ModeResource\Pages;

use App\Filament\Resources\ModeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModes extends ListRecords
{
    protected static string $resource = ModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
