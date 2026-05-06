<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaLog extends Model
{
    protected $table = 'wa_logs';
    protected $fillable = [
        'nomor_tujuan',
        'nama_penerima',
        'pesan',
        'status',
        'response_api'
    ];
}
