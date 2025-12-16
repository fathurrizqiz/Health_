<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeUtama extends Model
{
    protected $table = 'periode_detail_internal';
    protected $fillable = [
        'detail_id',
        'tanggal',
        'nama_pengajar',
        'tempat'
    ];

    public function detail()
    {
        return $this->belongsTo(DetailInternal::class, 'detail_id');
    }

    public function aksi()
    {
        return $this->hasMany(AksiDetailInternal::class, 'periode_id');
    }

    public function pembahasan()
    {
        return $this->hasMany(TemplatePembahasanSertifikat::class, 'periode_id');
    }

}
