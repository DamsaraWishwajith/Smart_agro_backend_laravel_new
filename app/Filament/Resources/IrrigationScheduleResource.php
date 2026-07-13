<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IrrigationScheduleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IrrigationScheduleResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $modelLabel = 'User Schedule Dashboard';
    
    protected static ?string $pluralModelLabel = 'Irrigation Schedules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('User Name')
                    ->disabled(),
                Forms\Components\TextInput::make('device_id')
                    ->label('Device ID')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('User ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device Name')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\UserResource\RelationManagers\IrrigationSchedulesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->has('irrigationSchedules');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIrrigationSchedules::route('/'),
            'view' => Pages\ViewIrrigationSchedule::route('/{record}'),
        ];
    }
}
