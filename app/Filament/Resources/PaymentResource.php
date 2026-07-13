<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('month')
                            ->label('Month')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (Rs.)')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                             ])
                            ->required(),
                        Forms\Components\TextInput::make('remark')
                            ->label('User Remark')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes / Rejection Reason')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('month')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (Rs.)')
                    ->money('LKR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                // View slip image
                Tables\Actions\Action::make('view_slip')
                    ->label('View Slip')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalContent(function (Payment $record) {
                        if (!$record->slip_image) {
                            return view('filament.pages.no-slip');
                        }
                        $url = '/storage/' . $record->slip_image;
                        $isPdf = strtolower(pathinfo($record->slip_image, PATHINFO_EXTENSION)) === 'pdf';
                        if ($isPdf) {
                            return new \Illuminate\Support\HtmlString(
                                '<div class="flex flex-col items-center justify-center p-4 gap-4">
                                    <div class="flex items-center gap-2 text-danger">
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="font-bold text-gray-700">PDF Document</span>
                                    </div>
                                    <a href="' . $url . '" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium transition inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Open PDF in New Tab
                                    </a>
                                    <iframe src="' . $url . '" class="w-full h-96 border rounded mt-2"></iframe>
                                </div>'
                            );
                        }
                        return new \Illuminate\Support\HtmlString(
                            '<div class="flex justify-center p-4">
                                <img src="' . $url . '" class="max-w-full max-h-96 rounded-lg shadow" />
                            </div>'
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // Approve action
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('This will approve the payment and notify the user.')
                    ->hidden(fn (Payment $record): bool => $record->status === 'approved')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);

                        // Send FCM push notification to user
                        $user = $record->user;
                        if ($user) {
                            $user->sendPushNotification(
                                '✅ Payment Approved',
                                'Your Rs.1,000 payment for ' . $record->month . ' has been approved. Thank you!'
                            );
                        }

                        Notification::make()
                            ->title('Payment approved and user notified.')
                            ->success()
                            ->send();
                    }),

                // Reject action with reason
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Payment')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason for Rejection')
                            ->required()
                            ->placeholder('e.g. Slip image is unclear, wrong amount, etc.'),
                    ])
                    ->hidden(fn (Payment $record): bool => $record->status === 'rejected')
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                        ]);

                        // Send FCM push notification to user
                        $user = $record->user;
                        if ($user) {
                            $user->sendPushNotification(
                                '❌ Payment Rejected',
                                'Your payment for ' . $record->month . ' was rejected. Reason: ' . $data['admin_notes']
                            );
                        }

                        Notification::make()
                            ->title('Payment rejected and user notified.')
                            ->danger()
                            ->send();
                    }),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
