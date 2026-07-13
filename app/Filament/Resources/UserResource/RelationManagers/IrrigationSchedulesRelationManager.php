<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IrrigationSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'irrigationSchedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('system_type')
                    ->options([
                        'drip' => 'Drip Irrigation',
                        'mist' => 'Mist Irrigation',
                        'exhaust' => 'Exhaust Fan',
                        'light' => 'Light System',
                    ])
                    ->required(),
                Forms\Components\TagsInput::make('days')
                    ->placeholder('e.g. Mon, Tue, Wed')
                    ->required(),
                Forms\Components\TimePicker::make('on_time')
                    ->required(),
                Forms\Components\TimePicker::make('off_time')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('system_type')
            ->columns([
                Tables\Columns\TextColumn::make('system_type')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('on_time')
                    ->sortable()
                    ->time('h:i A'),
                Tables\Columns\TextColumn::make('off_time')
                    ->sortable()
                    ->time('h:i A'),
                Tables\Columns\TextColumn::make('days')
                    ->searchable()
                    ->badge()
                    ->separator(','),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
