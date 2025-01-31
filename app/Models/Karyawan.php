<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class Karyawan extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = [
        'update_at',
        'nip',
        'nama',
        'email',
        'password',
        'alamat',
        'jenis_kelamin',
        'tanggal_lahir',
        'foto',
        'no_telp',
        'face_id',
        'id_device',
        'id_shift',
        'id_office',
        'wfa',
        'status',
    ];

    public function setPasswordAttribute($value)
    {
        if (!empty($value) && $value != $this->getOriginal('password')) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->toDateTimeString();
    }

    public function office()
    {
        return $this->belongsTo(Offices::class, 'id_office');
    }

    public function shift()
    {
        return $this->belongsTo(Shifts::class, 'id_shift');
    }

    public function izin()
    {
        return $this->hasMany(Izin::class, 'id_karyawan');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'id_karyawan');
    }
}
