<?php

namespace App\Observers;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Storage;

class KaryawanObserver
{
    /**
     * php artisan make:observer izinObserver --model=izin
     */

    // Cek jika ada file foto baru
    public function updated(Karyawan $karyawan): void
    {

        if ($karyawan->isDirty('foto')) {
            $oldPhoto = $karyawan->getOriginal('foto');
            if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }
    }




    public function deleted(Karyawan $karyawan): void
    { {
            if ($karyawan->foto) {
                $filePath = 'public/' . $karyawan->foto;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        }
    }
}
