<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AksiDetailInternal extends Model
{
    protected $table = 'aksi_detail_internal';
    protected $fillable = [
        'periode_id',
        'jam_diklat',
    ];

    public function periodeUtama()
    {
        return $this->belongsTo(PeriodeUtama::class, 'periode_id');
    }
    public function evaluasi()
    {
        return $this->hasOne(EvaluasiDetailInternal::class, 'detail_id');
    }
}
