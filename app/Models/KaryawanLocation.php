<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_karyawan',
        'location',
        'distance',
        'latitude',
        'longitude',
        'id_device',
   ];

   public function karyawan()
   {
       return $this->belongsTo(Karyawan::class, 'id_karyawan');
   }
}
