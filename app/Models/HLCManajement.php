<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HLCManajement extends Model
{
    protected $table = 'diklat_hlc';
    protected $fillable = [
        'program_id',
        'nama_diklat',
        'pengajar',
        'penyelenggara',
        'diklat',
        'tanggal_mulai',
        'tanggal_selesai',
        'nrp',
        'jam_diklat',
        'status'
    ];

    public function program()
    {
        return $this->belongsTo(ProgramHlc::class, 'program_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawans::class, 'nrp', 'nrp');
    }

}
