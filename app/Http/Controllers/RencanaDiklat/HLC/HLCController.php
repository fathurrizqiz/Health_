<?php

namespace App\Http\Controllers\RencanaDiklat\HLC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HLCController extends Controller
{
    public function index()
    {
        return Inertia::render('RencanaDiklat/HLC/index');
    }
}
