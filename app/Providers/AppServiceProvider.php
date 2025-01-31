<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Izin;
use App\Models\Karyawan;
use App\Observers\AttendanceObserver;
use App\Observers\IzinObserver;
use App\Observers\KaryawanObserver;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Izin::observe(IzinObserver::class);
        Karyawan::observe(KaryawanObserver::class);
        Attendance::observe(AttendanceObserver::class);
        FilamentColor::register([
            'danger' => Color::hex('#FE0004'),
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Blue,
            'success' => Color::hex('#10E000'),
            'warning' => Color::Amber,
            'white' => Color::hex('#FFFEFE')
        ]);

    }
}
//Karyawan::observe(KaryawanObserver::class);
