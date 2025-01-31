<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class InfoAttendanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::today();

        $absentTodayCount = Attendance::whereDate('created_at', $now)->distinct('id_karyawan')->whereHas('karyawan', function ($query) {
            $query->where('status', 'Aktif');
        })->count('id_karyawan');

        $totalKaryawanCount = Karyawan::count();

        $belumAbsenCount = $totalKaryawanCount - $absentTodayCount;

        return [
            Stat::make($now->format('d/M/Y'), $absentTodayCount)->color('success')
                ->description('karyawan absen hari ini')
                ->descriptionColor('success'),
            Stat::make($now->format('d/M/Y'), $belumAbsenCount)->color('danger')
                ->description('karyawan belum absen hari ini')
                ->descriptionColor('danger'),
        ];
    }
}
