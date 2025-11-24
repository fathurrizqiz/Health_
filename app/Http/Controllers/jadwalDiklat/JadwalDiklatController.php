<?php

namespace App\Http\Controllers\jadwalDiklat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalDiklatController extends Controller
{
    public function index()
    {
        return Inertia::render('RencanaDiklat/jadwaldiklat/index');
    }
}
