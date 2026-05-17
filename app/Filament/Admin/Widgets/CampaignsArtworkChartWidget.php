<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Campaign;
use Filament\Widgets\ChartWidget;

class CampaignsArtworkChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Admin Artwork vs Client Artwork';

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $needsAdmin  = Campaign::where('needs_admin_artwork', true)->count();
        $ownArtwork  = Campaign::where('needs_admin_artwork', false)->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Campaigns',
                    'data'            => [$needsAdmin, $ownArtwork],
                    'backgroundColor' => ['#f59e0b', '#6366f1'],
                ],
            ],
            'labels' => [
                'Admin Artwork (1,300 SAR fee)',
                'Client Uploads Own Artwork',
            ],
        ];
    }
}
