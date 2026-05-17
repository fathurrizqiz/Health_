<?php

namespace App\Http\Middleware;

use App\Models\DiklatEksternal;
use App\Models\HLCManajement;
use App\Models\PeriodeUtama;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $countJadwal = 0;
        $countPersetujuan = 0;
        $countInbox = 0;

        if ($user = $request->user()) {
            $nrp = $user->nrp;
            $today = Carbon::today();

            // 1. Hitung Persetujuan (Hanya jika role user punya akses admin biasanya)
            // Jika semua user bisa melihat ini, biarkan saja. 
            // Jika hanya admin, bisa ditambah if(in_array('admin_diklat', $user->role))
            $countPersetujuan = DiklatEksternal::where('status', 'pending')->count() +
                HLCManajement::where('status', 'pending')->count();

            // 2. Hitung Jadwal
            $internalCount = PeriodeUtama::whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
                ->where('tanggal', '>=', $today)
                ->count();

            $hlcCount = HLCManajement::where('nrp', $nrp)
                ->where('status', 'pending') // Pastikan statusnya pending (sudah di-acc user)
                ->where('tanggal_mulai', '>=', $today)
                ->count();

            $eksternalCount = DiklatEksternal::where('nrp', $nrp)
                ->where('tanggal_mulai', '>=', $today)
                ->count();

            $countJadwal = $internalCount + $hlcCount + $eksternalCount;

            // 3. Hitung Inbox (Undangan Baru)
            $countInbox = HLCManajement::where('nrp', $nrp)
                ->where('status', 'offered')
                ->count();
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
                    // AMBIL DATA ROLE DARI SPATIE DI SINI
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
                'is_impersonating' => $request->session()->has('impersonator_id'),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'notifications' => [
                'jadwal_count' => $countJadwal,
                'persetujuan_count' => $countPersetujuan,
                'InboxCount' => $countInbox,
            ],
        ];
    }
}
