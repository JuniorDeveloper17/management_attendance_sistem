<?php

namespace App\Filament\Resources\OfficesResource\Pages;

use App\Filament\Resources\OfficesResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOffices extends EditRecord
{
    protected static string $resource = OfficesResource::class;
    protected static ?string $title = 'Ubah Office';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->action(function ($record) {
                try {
                    $hasKaryawan = \App\Models\Karyawan::where('id_office', $record->id)
                    ->exists();
                    if ($hasKaryawan) {
                        throw new \Exception("Office ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.");
                    }
                    $record->delete();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Gagal Menghapus Office')
                        ->body('Office ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.')
                        ->danger()
                        ->color('danger')
                        ->send();
                }
            })
        ];
    }
}
