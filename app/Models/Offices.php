<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Offices extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'address',
        'latitude',
        'longitude',
        'radius',
   ];


   public function karyawan()
   {
       return $this->hasMany(Karyawan::class, 'id_office');
   }
}
