<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\AttendanceResource\Widgets\InfoAttendanceWidget;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected static ?string $title = 'Kehadiran';
    protected function getHeaderWidgets(): array
    {
        return [
            //InfoAttendanceWidget::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exporter(AttendanceExporter::class)
                ->label('Export')
                ->color('success')
                ->size(ActionSize::Large)
                ->icon('heroicon-o-arrow-down-tray')
                ->formats( [ExportFormat::Xlsx])
                ->chunkSize(1000)
        ];
    }
}
