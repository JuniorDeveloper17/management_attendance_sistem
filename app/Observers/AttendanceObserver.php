<?php

namespace App\Observers;

use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        //
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function updated(Attendance $attendance): void
    {
        //
    }

    /**
     * Handle the Attendance "deleted" event.
     */
    public function deleted(Attendance $attendance): void
    {

        if ($attendance->checkin_image) {
            $filePath = 'public/' . $attendance->checkin_image;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }
        if ($attendance->checkout_image) {
            $filePath = 'public/' . $attendance->checkout_image;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }
    }

    /**
     * Handle the Attendance "restored" event.
     */
    public function restored(Attendance $attendance): void
    {
        //
    }

    /**
     * Handle the Attendance "force deleted" event.
     */
    public function forceDeleted(Attendance $attendance): void
    {
        //
    }
}
