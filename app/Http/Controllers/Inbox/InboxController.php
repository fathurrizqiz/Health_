<?php

namespace App\Http\Controllers\Inbox;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\HLCManajement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InboxController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $undangan = HLCManajement::where('nrp', $user->nrp)
            ->where('status', 'offered') // Hanya tampilkan yang statusnya ditawarkan
            ->orderBy('created_at', 'desc')
            ->get();
        $undanganexternal = DiklatEksternal::where('nrp', $user->nrp)
            ->where('status', 'offered') // Hanya tampilkan yang statusnya ditawarkan
            ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('Inbox/index', [
            'inboxItems' => $undangan,
            'inboxExternalItems' => $undanganexternal
        ]);
    }
    public function setujuRekomendasihlc($id)
    {
        $hlc = HLCManajement::findOrFail($id);

        // Ubah status menjadi pending (artinya masuk jadwal tapi belum dilaksanakan)
        $hlc->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Diklat berhasil ditambahkan ke jadwal Anda.');
    }
    public function tolakRekomendasihlc($id)
    {
        $hlc = DiklatEksternal::findOrFail($id);

        // Ubah status menjadi rejected (artinya ditolak)
        $hlc->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Diklat berhasil ditambahkan ke jadwal Anda.');
    }
    public function setujuRekomendasieksternal($id)
    {
        $hlc = DiklatEksternal::findOrFail($id);

        // Ubah status menjadi pending (artinya masuk jadwal tapi belum dilaksanakan)
        $hlc->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Diklat berhasil ditambahkan ke jadwal Anda.');
    }
    public function tolakRekomendasieksternal($id)
    {
        $hlc = DiklatEksternal::findOrFail($id);

        // Ubah status menjadi rejected (artinya ditolak)
        $hlc->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Diklat berhasil ditambahkan ke jadwal Anda.');
    }

}
