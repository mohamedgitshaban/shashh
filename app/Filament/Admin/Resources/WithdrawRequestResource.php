<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WithdrawRequestResource\Pages;
use App\Models\WithdrawRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WithdrawRequestResource extends Resource
{
    protected static ?string $model = WithdrawRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Withdraw Requests';

    protected static ?string $navigationGroup = 'Fulfillment';

    protected static ?int $navigationSort = 2;

    // Companies create these through the API (balance is reserved at that point) —
    // admins only review, so no create/edit form is needed.
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.company_name')
                    ->label('Company')
                    ->description(fn (WithdrawRequest $record): ?string => $record->company?->email)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('account_holder_name')
                    ->label('Account Holder')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('iban')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (WithdrawRequest $record): bool => $record->status === WithdrawRequest::STATUS_PENDING)
                    ->form([
                        Forms\Components\FileUpload::make('proof')
                            ->label('Transfer Proof (screenshot/receipt)')
                            ->directory('withdraw_proofs')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'application/pdf'])
                            ->required(),
                    ])
                    ->modalHeading('Approve Withdrawal')
                    ->modalDescription(fn (WithdrawRequest $record): string => "Confirm the bank transfer of SAR {$record->amount} to {$record->account_holder_name} ({$record->iban}) has been made, then attach proof.")
                    ->modalSubmitActionLabel('Approve')
                    ->action(function (WithdrawRequest $record, array $data): void {
                        $record->approve(Auth::user(), $data['proof']);

                        Notification::make()
                            ->title('Withdrawal approved')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (WithdrawRequest $record): bool => $record->status === WithdrawRequest::STATUS_PENDING)
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(4)
                            ->placeholder('Explain why this withdrawal is being rejected...'),
                    ])
                    ->modalHeading('Reject Withdrawal')
                    ->modalDescription('The reserved amount will be refunded back to the company\'s balance.')
                    ->modalSubmitActionLabel('Reject')
                    ->action(function (WithdrawRequest $record, array $data): void {
                        $record->reject(Auth::user(), $data['reason']);

                        Notification::make()
                            ->title('Withdrawal rejected')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\Action::make('downloadProof')
                    ->label('Proof')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn (WithdrawRequest $record): bool => $record->has_invoice)
                    ->action(fn (WithdrawRequest $record) => response()->download(
                        Storage::path($record->proof_file),
                        "payout-{$record->id}-invoice." . pathinfo($record->proof_file, PATHINFO_EXTENSION)
                    )),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawRequests::route('/'),
        ];
    }
}
