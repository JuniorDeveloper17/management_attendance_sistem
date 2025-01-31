<?php

namespace App\Filament\Resources\IzinResource\Pages;

use App\Filament\Exports\IzinExporter;
use App\Filament\Resources\IzinResource;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListIzins extends ListRecords
{
    protected static string $resource = IzinResource::class;
    protected static ?string $title = 'Izin';

    protected function getHeaderActions(): array
    {
        return [

            ExportAction::make()->exporter(IzinExporter::class)
            ->label('Export')
            ->color('success')
            ->size(ActionSize::Large)
            ->icon('heroicon-o-arrow-down-tray')
            ->formats([ExportFormat::Xlsx])
            ->chunkSize(1000)
        ];
    }
}
