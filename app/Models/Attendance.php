<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_karyawan',
        'checkin_location',
        'checkout_location',
        'checkin_image',
        'checkout_image',
        'durasi_kerja',
        'status',
        'id_device',
        'id_shift',
        'id_office',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }
    public function office()
    {
        return $this->belongsTo(Offices::class, 'id_office');
    }

    public function shift()
    {
        return $this->belongsTo(Shifts::class, 'id_shift');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->toDateTimeString();
    }
}
