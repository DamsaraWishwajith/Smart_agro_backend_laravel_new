<?php

namespace App\Filament\Resources\ModeResource\Pages;

use App\Filament\Resources\ModeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMode extends EditRecord
{
    protected static string $resource = ModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
