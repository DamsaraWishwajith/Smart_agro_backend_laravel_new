<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MotorResource\Pages;
use App\Filament\Resources\MotorResource\RelationManagers;
use App\Models\Motor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MotorResource extends Resource
{
    protected static ?string $model = Motor::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Device Association')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('device_id')
                            ->required()
                            ->maxLength(191),
                    ])->columns(2),

                Forms\Components\Section::make('Motor Controls')
                    ->description('Current status of system components.')
                    ->schema([
                        Forms\Components\Select::make('drip')
                            ->options(['ON' => 'ON', 'OFF' => 'OFF'])
                            ->required()
                            ->default('OFF'),
                        Forms\Components\Select::make('mist')
                            ->options(['ON' => 'ON', 'OFF' => 'OFF'])
                            ->required()
                            ->default('OFF'),
                        Forms\Components\Select::make('exhaust')
                            ->options(['ON' => 'ON', 'OFF' => 'OFF'])
                            ->required()
                            ->default('OFF'),
                        Forms\Components\Select::make('light')
                            ->options(['ON' => 'ON', 'OFF' => 'OFF'])
                            ->required()
                            ->default('OFF'),
                    ])->columns(4),
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
                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('drip')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ON' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('mist')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ON' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('exhaust')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ON' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('light')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ON' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Sync')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListMotors::route('/'),
            'create' => Pages\CreateMotor::route('/create'),
            'edit' => Pages\EditMotor::route('/{record}/edit'),
        ];
    }
}
