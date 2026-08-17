<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ScreenResource\Pages;
use App\Models\Screen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static ?string $navigationLabel = 'Screens';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Screen Details')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('screen_type')->disabled(),
                Forms\Components\TextInput::make('width')->disabled()->suffix('m'),
                Forms\Components\TextInput::make('height')->disabled()->suffix('m'),
                Forms\Components\TextInput::make('daily_impressions')->disabled(),
                Forms\Components\Textarea::make('description')->columnSpanFull()->disabled(),
            ]),

            Forms\Components\Section::make('Pricing & Schedule')->columns(2)->schema([
                Forms\Components\TextInput::make('price_per_day')->disabled()->prefix('SAR'),
                Forms\Components\TextInput::make('min_booking_days')->disabled()->suffix('days'),
                Forms\Components\TextInput::make('rotation_duration')->disabled()->suffix('seconds'),
                Forms\Components\TextInput::make('display_from')->disabled(),
                Forms\Components\TextInput::make('display_to')->disabled(),
                Forms\Components\Toggle::make('is_247')->disabled(),
                Forms\Components\Textarea::make('blackout_dates')->columnSpanFull()->disabled(),
            ]),

            Forms\Components\Section::make('Location')->columns(2)->schema([
                Forms\Components\TextInput::make('street_address')->columnSpanFull()->disabled(),
                Forms\Components\TextInput::make('landmark')->disabled(),
                Forms\Components\TextInput::make('district')->disabled(),
                Forms\Components\TextInput::make('city')->disabled(),
                Forms\Components\TextInput::make('latitude')->disabled(),
                Forms\Components\TextInput::make('longitude')->disabled(),
            ]),

            Forms\Components\Section::make('Review')->columns(2)->schema([
                Forms\Components\Select::make('approval_status')
                    ->options([
                        'in_review' => 'In Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                    ])->disabled(),
                Forms\Components\Textarea::make('rejection_reason')->columnSpanFull()->disabled(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('screen_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Price / Day')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'in_review',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'in_review' => 'In Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        default     => $state,
                    }),

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
                    ->options([
                        'in_review' => 'In Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Screen $record) => $record->approval_status === 'in_review')
                    ->action(function (Screen $record) {
                        $record->update([
                            'approval_status' => 'approved',
                            'reviewed_by'     => auth()->id(),
                            'reviewed_at'     => now(),
                            'rejection_reason' => null,
                        ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Screen $record) => $record->approval_status === 'in_review')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->minLength(10),
                    ])
                    ->action(function (Screen $record, array $data) {
                        $record->update([
                            'approval_status'  => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by'      => auth()->id(),
                            'reviewed_at'      => now(),
                        ]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('company');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScreens::route('/'),
            'view'  => Pages\ViewScreen::route('/{record}'),
        ];
    }
}
