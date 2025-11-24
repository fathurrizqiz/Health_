<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDataModels extends Model
{
    protected $table = 'master_data';
     protected $fillable = [
        'nama_karyawan',
        'tmt',
        'nrp',
        'bagian',
        'unit_kerja',
        'posisi_jabatan',
        'klinis_non_klinis',
        'jenis_kelamin',
    ];
    public $timestamps = false;
    protected $casts = [
        'tmt' => 'date',
        'klinis' => 'boolean',
    ];
}
