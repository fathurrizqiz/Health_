<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiDetailInternal extends Model
{
    protected $table = 'evaluasi_detail_internal';
    protected $fillable = [
        'detail_id',
        'evaluasi'
    ];
    public function aksi()
    {
        return $this->belongsTo(AksiDetailInternal::class, 'detail_id');
    }
}
