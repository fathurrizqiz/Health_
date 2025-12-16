<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeBagianDetailInternal extends Model
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
        'jenis_kelamin',
        'hadir_at',
        'post_done_at',
        'pree_done_at',
        'sertifikat_path',
        'sertifikat_generated_at',
    ];
}
