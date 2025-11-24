<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawans extends Model
{
    protected $table = 'karyawans';
    protected $fillable = [
        'nama_karyawan',
        'user_id',
        'tmt',
        'nrp',
        'bagian',
        'unit_kerja',
        'posisi_jabatan',
        'klinis_non_klinis',
        'jenis_kelamin'
    ];

    public function diklat()
    {
        return $this->hasMany(DiklatKaryawan::class, 'karyawan_id', 'id');
    }

}
