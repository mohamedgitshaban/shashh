<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Clients', Client::count())
                ->description('Registered clients')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Companies', Company::count())
                ->description(Company::where('approval_status', 'in_review')->count() . ' pending review')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Total Admins', Admin::count())
                ->description('Active admin accounts')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
        ];
    }
}
