<?php

namespace App\Filament\Resources\KaryawanResource\Widgets;

use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $karyawan = Karyawan::class;
        return [
            Stat::make('Jumlah Karyawan', $karyawan::all()->count())->icon('heroicon-o-user'),
            Stat::make('Laki Laki', $karyawan::where('jenis_kelamin', 'Laki Laki')->count()),
            Stat::make('Perempuan', $karyawan::where('jenis_kelamin', 'Perempuan')->count()),
        ];
    }
}
