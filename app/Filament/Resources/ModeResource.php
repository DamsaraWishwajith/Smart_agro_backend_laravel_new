<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModeResource\Pages;
use App\Filament\Resources\ModeResource\RelationManagers;
use App\Models\Mode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModeResource extends Resource
{
    protected static ?string $model = Mode::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('System Mode')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('mode')
                            ->options(['MANUAL' => 'Manual', 'AUTO' => 'Auto'])
                            ->required()
                            ->default('MANUAL'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('mode')
                    ->badge()
                    ->color(fn (string $state): string => strtoupper($state) === 'AUTO' ? 'success' : 'warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Changed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mode')
                    ->options(['MANUAL' => 'Manual', 'AUTO' => 'Auto']),
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
            'index' => Pages\ListModes::route('/'),
            'create' => Pages\CreateMode::route('/create'),
            'edit' => Pages\EditMode::route('/{record}/edit'),
        ];
    }
}
