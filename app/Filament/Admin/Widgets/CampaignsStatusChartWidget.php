<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Campaign;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CampaignsStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Campaigns by Status';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Campaign::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Campaigns',
                    'data'            => [
                        $counts['pending_approval'] ?? 0,
                        $counts['approved']         ?? 0,
                        $counts['live']             ?? 0,
                        $counts['completed']        ?? 0,
                        $counts['rejected']         ?? 0,
                    ],
                    'backgroundColor' => [
                        '#94a3b8', // pending – slate
                        '#3b82f6', // approved – blue
                        '#22c55e', // live – green
                        '#6366f1', // completed – indigo
                        '#ef4444', // rejected – red
                    ],
                ],
            ],
            'labels' => ['Pending Approval', 'Approved', 'Live', 'Completed', 'Rejected'],
        ];
    }
}
