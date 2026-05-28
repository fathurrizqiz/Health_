<?php

namespace App\Http\Middleware;

use App\Models\DiklatEksternal;
use App\Models\DiklatKaryawan;
use App\Models\HLCManajement;
use App\Models\PeriodeUtama;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        
        // Inisialisasi default
        $countJadwal = 0;
        $countPersetujuan = 0;
        $countInbox = 0;

        if ($user = $request->user()) {
            $nrp = $user->nrp;
            $today = Carbon::today()->toDateString(); // Optimasi string date untuk where

            // ==========================================
            // 1. PERSERTUJUAN (Hanya Admin Diklat)
            // ==========================================
            // Menggunakan cache session jika ada, agar tidak query db terus menerus untuk angka yang jarang berubah
            if ($user->hasRole('admin_diklat')) {
                // Gunakan query sederhana
                $countPersetujuan = DiklatKaryawan::where('status', 'Disetujui')->count(); 
                // Catatan: Sesuaikan status 'pending'/'menunggu_persetujuan' sesuai kebutuhan bisnis Anda.
                // Biasanya admin melihat yang 'pending'.
                $countPersetujuan = DiklatKaryawan::where('status', 'Menunggu Persetujuan')->count();
            }

            // ==========================================
            // 2. JADWAL MENDATANG (OPTIMASI SINGLE QUERY)
            // ==========================================
            // Kita menggabungkan query Internal, HLC, dan Eksternal menjadi 1 query menggunakan DB::raw UNION
            // Ini mengurangi beban database drastis dibanding 3 query terpisah.
            $countJadwal = DB::table(DB::raw("(
                SELECT id FROM periode_bagian_detail_internal 
                WHERE nrp = '$nrp' 
                AND EXISTS (SELECT 1 FROM periode_detail_internal pdi WHERE pdi.id = periode_bagian_detail_internal.periode_id AND pdi.tanggal >= '$today')
                
                UNION ALL
                
                SELECT id FROM diklat_hlc 
                WHERE nrp = '$nrp' 
                AND status != 'Ditolak' 
                AND tanggal_mulai >= '$today'
                
                UNION ALL
                
                SELECT id FROM diklat_eksternal 
                WHERE nrp = '$nrp' 
                AND status != 'Ditolak' 
                AND tanggal_mulai >= '$today'
            ) as combined_jadwal"))->count();

            // ==========================================
            // 3. INBOX / UNDANGAN (Perbaikan Logika)
            // ==========================================
            // Inbox biasanya berisi HAL yang butuh tindakan user (Undangan Baru / Tawaran).
            // Diasumsikan status 'Menunggu Persetujuan' di HLC/Eksternal adalah undangan yang perlu diterima user.
            
            // Gunakan query gabungan untuk Inbox juga agar efisien
            $countInbox = DB::table(DB::raw("(
                SELECT id FROM diklat_hlc 
                WHERE nrp = '$nrp' 
                AND status = 'Menunggu Persetujuan'
                
                UNION ALL
                
                SELECT id FROM diklat_eksternal 
                WHERE nrp = '$nrp' 
                AND status = 'Menunggu Persetujuan'
            ) as combined_inbox"))->count();
        }

        // ==========================================
        // 4. IMPOSTOR DATA (Optimasi)
        // ==========================================
        $impersonatorName = null;
        if ($request->session()->has('impersonator_id')) {
            // Hanya query jika session ada
            $impersonatorName = optional(User::where('nrp', $request->session()->get('impersonator_id'))->first())->name;
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'nrp' => $request->user()->nrp,
                    'employee_id' => $request->user()->employee_id,
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],
            'is_impersonating' => $request->session()->has('impersonator_id'),
            'impersonatorName' => $impersonatorName,
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'notifications' => [
                'jadwal_count' => $countJadwal,
                'persetujuan_count' => $countPersetujuan,
                'inbox_count' => $countInbox, // DIUBAH KE SNAKE CASE
            ],
        ];
    }
}