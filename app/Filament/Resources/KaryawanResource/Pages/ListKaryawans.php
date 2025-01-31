<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Exports\KaryawanExporter;
use App\Filament\Resources\KaryawanResource;
use App\Filament\Resources\KaryawanResource\Widgets\InfoWidget;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;
    protected static ?string $title = 'Karyawan';

    protected function getHeaderWidgets(): array
    {
        return [
            InfoWidget::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->size(ActionSize::Large)->label('buat karyawan'),
            ExportAction::make()->exporter(KaryawanExporter::class)
                ->label('Export')
                ->color('success')
                ->size(ActionSize::Large)
                ->icon('heroicon-o-arrow-down-tray')
                ->formats([ExportFormat::Xlsx])
                ->chunkSize(1000)
        ];
    }
}
