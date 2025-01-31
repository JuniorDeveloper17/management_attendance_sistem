<?php

namespace App\Filament\Resources\OfficesResource\Pages;

use App\Filament\Resources\OfficesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{
    protected static string $resource = OfficesResource::class;
    protected static ?string $title = 'Office';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Office'),
        ];
    }
}
