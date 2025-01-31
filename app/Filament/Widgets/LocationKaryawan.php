<?php

namespace App\Filament\Widgets;

use App\Models\KaryawanLocation;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class LocationKaryawan extends BaseWidget
{

    protected static ?string $heading = 'Lokasi Karyawan sekarang';

    public function table(Table $table): Table
    {
        $now  = now();
        return $table
            ->query(
                KaryawanLocation::query()->with('karyawan')->whereDate('updated_at', $now)
            )
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('NAMA KARYAWAN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('LOKASI')
                    ->description(function ($record) {
                        return $record->latitude . '-' . $record->longitude;
                    }),
                Tables\Columns\TextColumn::make('device')
                    ->label('ID PERANGKAT')
                    ->default(function ($record) {
                        return $record->id_device;
                    })
                    ->badge()
                    ->color(function ($record) {
                        return $record->id_device == $record->karyawan->id_device ? 'success' : 'danger';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('STATUS')
                    ->default(function ($record) {
                        $status = $record->karyawan->office->radius;
                        return $status >= $record->distance ? 'Didalam Area' : 'Diluar Area';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $status = $record->karyawan->office->radius;
                        return  $status >= $record->distance ? 'success' : 'danger';
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('update')
                    ->color(function ($state) {
                        $updatedAt = Carbon::parse($state);
                        $now = Carbon::now();
                        if ($updatedAt->diffInSeconds($now) <= 60) {
                            return 'success';
                        }
                        return 'danger';
                    }),
            ])->filters([
                SelectFilter::make('office')
                    ->relationship('karyawan.office', 'nama')
                    ->preload(),
                SelectFilter::make('shift')
                    ->relationship('karyawan.shift', 'nama')
                    ->preload(),
            ]);
    }
}
