<?php

namespace App\Filament\Resources\ModeResource\Pages;

use App\Filament\Resources\ModeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMode extends ViewRecord
{
    protected static string $resource = ModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
