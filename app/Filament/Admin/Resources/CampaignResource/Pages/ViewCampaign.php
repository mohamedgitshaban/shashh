<?php

namespace App\Filament\Admin\Resources\CampaignResource\Pages;

use App\Filament\Admin\Resources\CampaignResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Components\Section::make('Campaign Objective')->columns(2)->schema([
                Components\TextEntry::make('title')->columnSpanFull(),
                Components\TextEntry::make('description')->columnSpanFull(),
                Components\TextEntry::make('objective')->badge()->color('info'),
                Components\TextEntry::make('client.name')->label('Client'),
                Components\TextEntry::make('date_from')->date()->label('Start Date'),
                Components\TextEntry::make('date_to')->date()->label('End Date'),
            ]),

            Components\Section::make('Artwork')->columns(2)->schema([
                Components\IconEntry::make('needs_admin_artwork')
                    ->label('Admin Artwork Requested')
                    ->boolean(),
                Components\TextEntry::make('artwork_fee')
                    ->label('Artwork Fee')
                    ->money('SAR'),
                Components\TextEntry::make('artwork')
                    ->label('Artwork File')
                    ->columnSpanFull()
                    ->placeholder('No artwork uploaded'),
            ]),

            Components\Section::make('Performance & Status')->columns(2)->schema([
                Components\TextEntry::make('total_impressions')
                    ->label('Total Impressions')
                    ->numeric(),
                Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'gray',
                        'approved'         => 'info',
                        'live'             => 'success',
                        'completed'        => 'primary',
                        'rejected'         => 'danger',
                        default            => 'gray',
                    }),
                Components\TextEntry::make('rejection_reason')
                    ->columnSpanFull()
                    ->placeholder('N/A'),
            ]),

            Components\Section::make('Booked Screens')->schema([
                Components\RepeatableEntry::make('bookings')
                    ->schema([
                        Components\TextEntry::make('screen.name')->label('Screen'),
                        Components\TextEntry::make('screen.city')->label('City'),
                        Components\TextEntry::make('sale_price')->money('SAR')->label('Sale Price'),
                        Components\TextEntry::make('commission')->money('SAR'),
                        Components\TextEntry::make('net_earned')->money('SAR')->label('Net Earned'),
                        Components\TextEntry::make('status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending_approval' => 'gray',
                                'approved'         => 'info',
                                'live'             => 'success',
                                'completed'        => 'primary',
                                'rejected'         => 'danger',
                                default            => 'gray',
                            }),
                    ])
                    ->columns(6),
            ]),
        ]);
    }
}
