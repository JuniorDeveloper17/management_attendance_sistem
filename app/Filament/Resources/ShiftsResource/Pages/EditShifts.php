<?php

namespace App\Filament\Resources\ShiftsResource\Pages;

use App\Filament\Resources\ShiftsResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditShifts extends EditRecord
{
    protected static string $resource = ShiftsResource::class;
    protected static ?string $title = 'Ubah Shift';


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function ($record) {
                    try {
                        $hasKaryawan = \App\Models\Karyawan::where('id_shift', $record->id)->exists();
                        if ($hasKaryawan) {
                            throw new \Exception("Shift ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.");
                        }
                        $record->delete();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Menghapus Shift')
                            ->body('Shift ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.')
                            ->danger()
                            ->color('danger')
                            ->send();
                    }
                }),
        ];
    }
}
