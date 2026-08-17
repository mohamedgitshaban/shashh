<?php

namespace App\Filament\Admin\Resources\ScreenResource\Pages;

use App\Filament\Admin\Resources\ScreenResource;
use App\Models\Screen;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewScreen extends ViewRecord
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->approval_status === 'in_review')
                ->action(function () {
                    $this->record->update([
                        'approval_status' => 'approved',
                        'reviewed_by'     => auth()->id(),
                        'reviewed_at'     => now(),
                        'rejection_reason' => null,
                    ]);
                    $this->refreshFormData(['approval_status', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->approval_status === 'in_review')
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->minLength(10),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'approval_status'  => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                        'reviewed_by'      => auth()->id(),
                        'reviewed_at'      => now(),
                    ]);
                    $this->refreshFormData(['approval_status', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
