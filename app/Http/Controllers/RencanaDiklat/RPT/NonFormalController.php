<?php

namespace App\Http\Controllers\RencanaDiklat\RPT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NonFormalController extends Controller
{
    public function index()
    {
        return Inertia::render('RencanaDiklat/RPT/PendidikanNonFormal/index');
    }
}
