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

        if ($request->user()){
            $countPersetujuan = DiklatEksternal::where('status', 'pending')->count() + HLCManajement::where('status', 'pending')->count();
        }
        if ($request->user()) {
            $nrp = $request->user()->nrp;
            $today = Carbon::today();

            // Hitung total dari 3 sumber
            $internalCount = PeriodeUtama::whereHas('peserta', fn($q) => $q->where('nrp', $nrp))
                ->where('tanggal', '>=', $today)
                ->count();

            $hlcCount = HLCManajement::where('nrp', $nrp)
                ->where('tanggal_mulai', '>=', $today)
                ->count();

            $eksternalCount = DiklatEksternal::where('nrp', $nrp)
                ->where('tanggal_mulai', '>=', $today)
                ->count();

            $countJadwal = $internalCount + $hlcCount + $eksternalCount;
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'notifications' => [
                'jadwal_count' => $countJadwal,
                'persetujuan_count' => $countPersetujuan,
            ],
        ];
    }
}
