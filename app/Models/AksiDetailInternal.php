<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AksiDetailInternal extends Model
{
    protected $table = 'aksi_detail_internal';
    protected $fillable = [
        'periode_id',
        'periode',
        'jam_diklat',
    ];

    public function periodeUtama()
    {
        return $this->belongsTo(PeriodeUtama::class, 'periode_id');
    }
    
}
