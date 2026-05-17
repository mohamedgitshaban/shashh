<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Screen;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ScreensChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Screens by Approval Status';

    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $counts = Screen::query()
            ->select('approval_status', DB::raw('count(*) as total'))
            ->groupBy('approval_status')
            ->pluck('total', 'approval_status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Screens',
                    'data'            => [
                        $counts['in_review'] ?? 0,
                        $counts['approved']  ?? 0,
                        $counts['rejected']  ?? 0,
                    ],
                    'backgroundColor' => ['#f59e0b', '#22c55e', '#ef4444'],
                ],
            ],
            'labels' => ['In Review', 'Approved', 'Rejected'],
        ];
    }
}
