<?php

namespace App\Filament\Exports;

use App\Models\Attendance;
use Carbon\Carbon;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;


class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;


    public static function query(): Builder
    {
        return Attendance::query()
            ->select('created_at', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as tanggal"))
            ->toBase();
    }
    public static function getColumns(): array
    {

        return [
            ExportColumn::make('tanggal')
                ->label('TANGGAL')
                ->getStateUsing(fn($record) => Carbon::parse($record->created_at)->format('d-m-Y')),
            ExportColumn::make('karyawan.nama')->label('NAMA'),
            ExportColumn::make('karyawan.nip')->label('NIP'),
            ExportColumn::make('office.nama')->label('OFFICE'),
            ExportColumn::make('shift.nama')->label('SHIFT'),
            ExportColumn::make('durasi_kerja')->label('DURASI KERJA'),
            ExportColumn::make('status')->label('STATUS'),
            ExportColumn::make('created_at')
                ->label('WAKTU MASUK')
                ->formatStateUsing(fn($state) => Carbon::hasFormat($state, 'Y-m-d H:i:s') ? Carbon::parse($state)->format('H:i:s') : 'Invalid Date'),
            ExportColumn::make('updated_at')
                ->label('WAKTU KELUAR')
                ->formatStateUsing(fn($state) => Carbon::hasFormat($state, 'Y-m-d H:i:s') ? Carbon::parse($state)->format('H:i:s') : 'Invalid Date'),
            ExportColumn::make('checkin_location')->label('LOKASI MASUK'),
            ExportColumn::make('checkout_location')->label('LOKASI KELUAR'),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        $body = "Laporan Presensi " . $date . ".xlsx";

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getFileName(Export $export): string
    {
        $date = now()->format('d.m.Y i.s');
        return "Laporan Presensi " . $date . ".xlsx";
    }
    public  function onFailure(Export $export, \Throwable $exception): void
    {
        // Log the error (optional)
        logger()->error('Export failed: ' . $exception->getMessage());

        // Delete the export record from the database
        $export->delete();

        // Delete file from storage/filamentExport
        if (!empty($export->file_name)) {
            $filePath = 'filament-exports/' . $export->file_name;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }

        // Additional logic to handle related jobs
        DB::table('jobs')->where('id', $export->id)->delete();
        DB::table('exports')->where('id', $export->id)->delete();
        DB::table('job_batches')->where('id', $export->id)->delete();
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
