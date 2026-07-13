<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantResource\Pages;
use App\Filament\Resources\PlantResource\RelationManagers;
use App\Models\Plant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlantResource extends Resource
{
    protected static ?string $model = Plant::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Crop Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('crop_name')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('plants_count')
                            ->label('Total Plants')
                            ->numeric()
                            ->prefix('Qty:'),
                        Forms\Components\TextInput::make('planted_days')
                            ->label('Days Since Planted')
                            ->numeric()
                            ->suffix('days'),
                    ])->columns(2),

                Forms\Components\Section::make('Water & Environment')
                    ->schema([
                        Forms\Components\TextInput::make('temperature')
                            ->label('Target Temperature')
                            ->numeric()
                            ->suffix('°C'),
                        Forms\Components\TextInput::make('water_need_ml')
                            ->label('Daily Water Need')
                            ->numeric()
                            ->suffix('ml'),
                        Forms\Components\TextInput::make('water_time_seconds')
                            ->label('Watering Duration')
                            ->numeric()
                            ->suffix('seconds'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('crop_name')
                    ->label('Crop')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('plants_count')
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planted_days')
                    ->label('Age')
                    ->suffix(' days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Temp')
                    ->suffix('°C')
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_need_ml')
                    ->label('Water (ml)')
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Filter by Owner'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPlants::route('/'),
            'create' => Pages\CreatePlant::route('/create'),
            'edit' => Pages\EditPlant::route('/{record}/edit'),
        ];
    }
}
