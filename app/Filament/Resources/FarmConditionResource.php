<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FarmConditionResource\Pages;
use App\Filament\Resources\FarmConditionResource\RelationManagers;
use App\Models\FarmCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FarmConditionResource extends Resource
{
    protected static ?string $model = FarmCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sensor Data')
                    ->schema([
                        Forms\Components\TextInput::make('device_id')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('temp')
                            ->label('Temperature')
                            ->required()
                            ->numeric()
                            ->suffix('°C'),
                        Forms\Components\TextInput::make('humidity')
                            ->label('Humidity')
                            ->required()
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\TextInput::make('soil_moisture')
                            ->label('Soil Moisture')
                            ->numeric()
                            ->suffix('%'),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device ID')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('temp')
                    ->label('Temperature')
                    ->numeric()
                    ->sortable()
                    ->suffix('°C')
                    ->color('warning')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humidity')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->color('info')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('soil_moisture')
                    ->label('Soil Moisture')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reading Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmConditions::route('/'),
            'create' => Pages\CreateFarmCondition::route('/create'),
            'edit' => Pages\EditFarmCondition::route('/{record}/edit'),
        ];
    }
}
