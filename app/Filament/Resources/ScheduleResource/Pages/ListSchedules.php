<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;
    protected static ?string $title = 'Schedule';


    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
}
