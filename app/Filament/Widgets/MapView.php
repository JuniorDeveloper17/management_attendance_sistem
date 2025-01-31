<?php

namespace App\Filament\Widgets;

use App\Models\KaryawanLocation;
use App\Models\Offices;
use Filament\Widgets\Widget;
use Webbingbrasil\FilamentMaps\Actions\Action;
use Webbingbrasil\FilamentMaps\Actions\CenterMapAction;
use Webbingbrasil\FilamentMaps\Actions\FullpageAction;
use Webbingbrasil\FilamentMaps\Actions\ZoomAction;
use Webbingbrasil\FilamentMaps\Circle;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class MapView extends  MapWidget
{
    protected int | string | array $columnSpan = 2;

    protected bool $hasBorder = false;

    protected string $height = '500px';

    protected static ?string $pollingInterval = null;
    protected string | array  $tileLayerUrl = [
        'OpenStreetMap' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'OpenTopoMap' => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png'
    ];
    protected array $tileLayerOptions = [
        'OpenStreetMap' => [
            'attribution' => 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        ],
        'OpenTopoMap' => [
            'attribution' => 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, SRTM | Map style © <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
        ],
    ];

    public function getMarkers(): array
    {
        $now  = now();
        $office = Offices::get();
        $karyawan = KaryawanLocation::query()->with('karyawan')->whereDate('updated_at', $now);

        $markers = [];
        if ($office->isEmpty() && $karyawan->isEmpty()) {
            return [];
        }
        foreach ($karyawan as $data) {
            $markers[] = Marker::make('pos' . $data->latitude . $data->longitude . '')
                ->lat((float) $data->latitude)
                ->lng((float)$data->longitude)->tooltip($data->karyawan->nama)
                ->popup($data->karyawan->nama)->color(Marker::COLOR_GREEN);
        }

        foreach ($office as $data) {
            $markers[] = Marker::make('pos' . $data->latitude . $data->longitude . '')
                ->lat((float) $data->latitude)
                ->lng((float)$data->longitude)->tooltip($data->nama)
                ->popup($data->nama)->color(Marker::COLOR_RED);
        }

        return $markers;
    }

    public function getCircles(): array
    {
        $office = Offices::get();

        $markers = [];
        if ($office->isEmpty()) {
            return [];
        }

        foreach ($office as $data) {
            $markers[] = Circle::make('pos' . $data->latitude . $data->longitude . '')
                ->lat((float) $data->latitude)
                ->lng((float)$data->longitude)
                ->tooltip($data->nama)
                ->options(['radius' => $data->radius]);
        }

        return $markers;
    }

    public function getActions(): array
    {
        return [
            FullpageAction::make(),
            ZoomAction::make(),
            Action::make('mode')
                ->icon('filamentmapsicon-o-square-3-stack-3d')
                ->alpineClickHandler('setTileLayer(mode === "OpenStreetMap" ? "OpenTopoMap" : "OpenStreetMap")'),
            CenterMapAction::make()->zoom(10)->centerTo([-3.2757738128200384, 102.96572318650246])
        ];
    }
}
