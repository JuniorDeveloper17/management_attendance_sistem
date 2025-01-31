<?php

namespace App\Filament\Exports;

use App\Models\Izin;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class IzinExporter extends Exporter
{
    protected static ?string $model = Izin::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('karyawan.nama')->label('NAMA'),
            ExportColumn::make('karyawan.nip')->label('NIP'),
            ExportColumn::make('keterangan')->label('JENIS IZIN'),
            ExportColumn::make('keterangan_lanjutan')->label('KETERANGAN')->formatStateUsing(fn($state) => $state ?: 'Tidak Ada'),
            ExportColumn::make('created_at')->label('TANGGAL PENGAJUAN')
            ->formatStateUsing(function ($state) {
                return Carbon::parse($state)->format('d-m-Y');
            }),
            ExportColumn::make('status')->label('PERSETUJUAN')
                ->formatStateUsing(function ($state) {
                    return $state ? 'Ya' : 'Tidak';
                }),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        $body = "Laporan Izin " . $date . ".xlsx";

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getFileName(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        return "Laporan Izin " . $date . ".xlsx";
    }

    public function getXlsxCellStyle(): ?Style
    {
        $border = new Border(
            new BorderPart(Border::TOP, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM),
            new BorderPart(Border::BOTTOM, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM),
            new BorderPart(Border::LEFT, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM),
            new BorderPart(Border::RIGHT, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM)
        );
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Aptos')
            ->setBorder($border);
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        $border = new Border(
            new BorderPart(
                Border::TOP,
                Color::rgb(0, 0, 0),
                Border::WIDTH_MEDIUM
            ),
            new BorderPart(Border::BOTTOM, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM),
            new BorderPart(Border::LEFT, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM),
            new BorderPart(Border::RIGHT, Color::rgb(0, 0, 0), Border::WIDTH_MEDIUM)
        );
        return (new Style())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontName('Aptos')
            ->setFontColor(Color::rgb(255, 255, 255))
            ->setBackgroundColor(Color::rgb(12, 161, 12))
            ->setBorder($border)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
 
}
