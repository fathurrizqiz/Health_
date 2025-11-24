<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanFormalModels extends Model
{
    protected $table = 'pendidikan_formal';
    protected $fillable = [
        'nama_program',
        'kategori',
        'tahun'
    ];
}
