<?php

namespace App\Filament\Admin\Resources\WithdrawRequestResource\Pages;

use App\Filament\Admin\Resources\WithdrawRequestResource;
use App\Models\WithdrawRequest;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWithdrawRequests extends ListRecords
{
    protected static string $resource = WithdrawRequestResource::class;

    // No CreateAction — companies submit these via the API, not admins.
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawRequest::STATUS_PENDING))
                ->badge(fn () => WithdrawRequest::where('status', WithdrawRequest::STATUS_PENDING)->count()),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawRequest::STATUS_APPROVED)),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawRequest::STATUS_REJECTED)),
        ];
    }
}
