<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\izin;
use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class DashboardHeaderOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $user = auth()->user();
      

        $hour = now()->format('H');
        $absensi = Attendance::whereDate('created_at', now())->count();
        $izin = izin::whereDate('created_at', now())->count();

        $time = '';
        if ($hour >= 19) {
            $time = 'Selamat Malam';
        } else if ($hour >= 16) {
            $time = 'Selamat Sore';
        } else if ($hour >= 11) {
            $time = 'Selamat Siang';
        } else if ($hour >= 5) {
            $time = 'Selamat Pagi';
        } else {
            $time = 'Selamat Malam';
        }


        return [
            Stat::make('', '')
                ->color('white')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                    'style' => 'background-color:#202AFF;',
                ])
                ->label(new HtmlString('<p style="color:white">' . $time . '</p>'))
                ->value(new HtmlString('<h1 style="color:white">' . $user->name . '</h1>'))
                ->description('selamat datang kembali di panel admin'),

            Stat::make('', '')
                ->color('white')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                    'style' => 'background-color:#00BD13;',
                ])
                ->label(new HtmlString('<p style="color:white">presensi hari ini</p>'))
                ->value(new HtmlString('<h1 style="color:white">Karyawan</h1>'))
                ->description($absensi . ' karyawan telah melakukan presensi hari ini'),

            Stat::make('', '')
                ->color('white')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                    'style' => 'background-color:#C40100;',
                ])
                ->label(new HtmlString('<p style="color:white">izin hari ini</p>'))
                ->value(new HtmlString('<h1 style="color:white">Karyawan</h1>'))
                ->description($izin . ' karyawan mengajukan izin hari ini'),

        ];
    }
}
