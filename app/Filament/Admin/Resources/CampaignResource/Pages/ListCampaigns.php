<?php

namespace App\Filament\Admin\Resources\CampaignResource\Pages;

use App\Filament\Admin\Resources\CampaignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'pending_approval' => Tab::make('Pending Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending_approval')),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),

            'live' => Tab::make('Live')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'live')),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
