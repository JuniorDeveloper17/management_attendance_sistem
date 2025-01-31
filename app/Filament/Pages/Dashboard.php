<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardHeaderOverview;
use App\Filament\Widgets\LocationKaryawan;
use App\Filament\Widgets\MapView;
use App\Filament\Widgets\TableAbsensNow;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $navigationLabel = 'Dashboard';

    public function getColumns(): int | string | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            DashboardHeaderOverview::class,
            MapView::class,
            LocationKaryawan::class
        ];
    }
}
