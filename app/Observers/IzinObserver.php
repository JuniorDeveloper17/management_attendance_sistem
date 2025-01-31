<?php

namespace App\Observers;

use App\Models\Izin;
use Illuminate\Support\Facades\Storage;

class IzinObserver
{
    /**
     * Handle the izin "created" event.
     */
    public function created(Izin $izin): void
    {
        //
    }

    /**
     * Handle the izin "updated" event.
     */
    public function updated(Izin $izin): void
    {
    
    }

    /**
     * Handle the izin "deleted" event.
     */
    public function deleted(Izin $izin): void
    {
        if ($izin->document) {
            $filePath = 'public/' . $izin->document;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }
    }

    /**
     * Handle the izin "restored" event.
     */
    public function restored(Izin $izin): void
    {
        //
    }

    /**
     * Handle the izin "force deleted" event.
     */
    public function forceDeleted(Izin $izin): void
    {
        //
    }
}
