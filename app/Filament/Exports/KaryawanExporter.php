<?php

namespace App\Filament\Exports;

use App\Models\Karyawan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;



class KaryawanExporter extends Exporter
{
    protected static ?string $model = Karyawan::class;
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nip')->label('NIP'),
            ExportColumn::make('nama')->label('NAMA'),
            ExportColumn::make('email')->label('EMAIL'),
            ExportColumn::make('alamat')->label('ALAMAT'),
            ExportColumn::make('jenis_kelamin')->label('JENIS KELAMIN'),
            ExportColumn::make('tanggal_lahir')->label('TANGGAL LAHIR'),
            ExportColumn::make('no_telp')->label('NOMOR TELP'),
            ExportColumn::make('shift.nama')->label('SHIFT'),
            ExportColumn::make('office.nama')->label('OFFICE'),
            ExportColumn::make('wfa')->label('STATUS PEGAWAI')
                ->formatStateUsing(function ($state) {
                    return $state ? 'WFA' : 'WHO';
                }),
            ExportColumn::make('jumlah_izin')->label('JUMLAH IZIN')
                ->formatStateUsing(function ($record) {
                    return $record->izin ? $record->izin()->count() : 0;
                }),

            ExportColumn::make('jumlah_absensi')->label('JUMLAH KEHADIRAN')
                ->formatStateUsing(function ($record) {
                    return $record->attendance ? $record->attendance()->count() : 0;
                }),

        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        $body = "Export Karyawan " . $date . ".xlsx";

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getFileName(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        return "Export Karyawan " . $date . ".xlsx";
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
