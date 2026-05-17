<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Campaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CampaignsTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'New Campaigns per Month (Last 12 Months)';

    protected static ?int $sort = 6;

    protected static string $color = 'primary';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $rows = Campaign::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Build a full 12-month range so months with 0 campaigns still appear
        $labels = [];
        $data   = [];
        for ($i = 11; $i >= 0; $i--) {
            $key      = now()->subMonths($i)->format('Y-m');
            $label    = now()->subMonths($i)->format('M Y');
            $labels[] = $label;
            $data[]   = $rows[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Campaigns',
                    'data'            => $data,
                    'backgroundColor' => '#0033e7',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
