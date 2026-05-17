<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CampaignResource\Pages;
use App\Filament\Admin\Resources\CampaignResource\Pages\ViewCampaign;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Campaigns';
    protected static ?string $navigationGroup = 'Campaigns';
    protected static ?int    $navigationSort  = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Objective')->columns(2)->schema([
                Forms\Components\TextInput::make('title')
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('objective')
                    ->disabled(),

                Forms\Components\TextInput::make('client.name')
                    ->label('Client')
                    ->disabled(),

                Forms\Components\DatePicker::make('date_from')
                    ->disabled(),

                Forms\Components\DatePicker::make('date_to')
                    ->disabled(),
            ]),

            Forms\Components\Section::make('Artwork')->columns(2)->schema([
                Forms\Components\Toggle::make('needs_admin_artwork')
                    ->disabled()
                    ->label('Admin Artwork Requested'),

                Forms\Components\TextInput::make('artwork_fee')
                    ->disabled()
                    ->prefix('SAR')
                    ->label('Artwork Fee'),

                Forms\Components\TextInput::make('artwork')
                    ->disabled()
                    ->label('Artwork File Path')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Performance')->columns(2)->schema([
                Forms\Components\TextInput::make('total_impressions')
                    ->disabled()
                    ->label('Total Impressions (accumulated)'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'approved'         => 'Approved',
                        'live'             => 'Live',
                        'completed'        => 'Completed',
                        'rejected'         => 'Rejected',
                    ])
                    ->disabled(),

                Forms\Components\Textarea::make('rejection_reason')
                    ->disabled()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('objective')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('date_from')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_to')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('needs_admin_artwork')
                    ->label('Admin Artwork')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('artwork_fee')
                    ->label('Artwork Fee')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_impressions')
                    ->label('Impressions')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'gray',
                        'approved'         => 'info',
                        'live'             => 'success',
                        'completed'        => 'primary',
                        'rejected'         => 'danger',
                        default            => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'approved'         => 'Approved',
                        'live'             => 'Live',
                        'completed'        => 'Completed',
                        'rejected'         => 'Rejected',
                    ]),

                Tables\Filters\TernaryFilter::make('needs_admin_artwork')
                    ->label('Admin Artwork Requested')
                    ->trueLabel('Yes – admin artwork')
                    ->falseLabel('No – client artwork'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Start From'),
                        Forms\Components\DatePicker::make('to')->label('Start To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $v) => $q->whereDate('date_from', '>=', $v))
                            ->when($data['to'],   fn ($q, $v) => $q->whereDate('date_from', '<=', $v));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('client');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'view'  => Pages\ViewCampaign::route('/{record}'),
        ];
    }
}
