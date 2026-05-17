<?php

namespace App\Filament\Admin\Resources\ScreenResource\Pages;

use App\Filament\Admin\Resources\ScreenResource;
use App\Models\Screen;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Screen::count()),

            'in_review' => Tab::make('In Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'in_review'))
                ->badge(Screen::where('approval_status', 'in_review')->count())
                ->badgeColor('warning'),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'approved'))
                ->badge(Screen::where('approval_status', 'approved')->count())
                ->badgeColor('success'),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('approval_status', 'rejected'))
                ->badge(Screen::where('approval_status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }
}
