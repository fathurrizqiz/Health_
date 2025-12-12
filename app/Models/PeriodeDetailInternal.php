<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeDetailInternal extends Model
{
    protected $table = 'periode_bagian_detail_internal';
    protected $fillable = [
        'detail_program_id',
        'nama_karyawan',
        'tmt',
        'nrp',
        'bagian',
        'unit_kerja',
        'posisi_jabatan',
        'klinis_non_klinis',
        'jenis_kelamin'
    ];
}
