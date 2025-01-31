<?php

namespace App\Filament\Resources\OfficesResource\Pages;

use App\Filament\Resources\OfficesResource;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\CreateRecord;

class CreateOffices extends CreateRecord
{
    protected static string $resource = OfficesResource::class;
    protected static ?string $title = 'Buat Office Baru';


}
