<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(3),

                Forms\Components\Section::make('Company Details')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('vat_number')
                            ->maxLength(50),
                        Forms\Components\FileUpload::make('cr')
                            ->label('CR Document (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('company_docs')
                            ->downloadable(),
                    ])->columns(2),

                Forms\Components\Section::make('Review Status')
                    ->schema([
                        Forms\Components\Select::make('approval_status')
                            ->options([
                                'in_review' => 'In Review',
                                'approved'  => 'Approved',
                                'rejected'  => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get): bool => $get('approval_status') === 'rejected'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'in_review',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_review' => 'In Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        default     => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Status')
                    ->options([
                        'in_review' => 'In Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Company')
                    ->modalDescription('Are you sure you want to approve this company account?')
                    ->visible(fn (Company $record): bool => $record->approval_status === 'in_review')
                    ->action(function (Company $record): void {
                        $record->update([
                            'approval_status' => 'approved',
                            'rejection_reason' => null,
                            'reviewed_by'     => Auth::id(),
                            'reviewed_at'     => now(),
                        ]);

                        Notification::make()
                            ->title('Company approved successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Company $record): bool => $record->approval_status === 'in_review')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(4)
                            ->placeholder('Explain why this company is being rejected...'),
                    ])
                    ->modalHeading('Reject Company')
                    ->modalSubmitActionLabel('Reject')
                    ->action(function (Company $record, array $data): void {
                        $record->update([
                            'approval_status'  => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by'      => Auth::id(),
                            'reviewed_at'      => now(),
                        ]);

                        Notification::make()
                            ->title('Company rejected')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
        ];
    }
}
