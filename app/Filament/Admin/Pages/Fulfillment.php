<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\CampaignProgressChartWidget;
use App\Filament\Admin\Widgets\FulfillmentStatsWidget;
use Filament\Pages\Page;

class Fulfillment extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $navigationGroup = 'Fulfillment';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Fulfillment Dashboard';

    protected static ?string $slug = 'fulfillment';

    protected static string $view = 'filament.admin.pages.fulfillment';

    protected function getHeaderWidgets(): array
    {
        return [
            FulfillmentStatsWidget::class,
            CampaignProgressChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return 1;
    }
}
