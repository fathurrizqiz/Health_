<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DiklatEksternal;
use App\Models\DiklatKaryawan;
use App\Models\HLCManajement;
use App\Models\Karyawans;
use App\Models\PeriodeBagianDetailInternal;
use App\Models\RekapJamDiklat;
use App\Models\TargetJamModels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $karyawan = Karyawans::count();
        $year = now()->year;

        // Aktual per bagian
        $aktualPerBagian = Karyawans::when($search, function ($query, $search) {
            $query->where('bagian', 'ILIKE', "%{$search}%");
        })
            ->join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
            ->where('rekap_jam_diklat.tahun', $year)
            ->select('karyawans.bagian')
            ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual')
            ->groupBy('karyawans.bagian')
            ->get()
            ->pluck('total_aktual', 'bagian');

        // Total per bagian
        $totalPerBagian = Karyawans::when($search, function ($query, $search) {
            $query->where('bagian', 'ILIKE', "%{$search}%");
        })
            ->select('bagian')
            ->selectRaw('COUNT(*) as total_karyawan')
            ->groupBy('bagian')
            ->get();

        $dataFinal = $totalPerBagian->map(function ($row) use ($aktualPerBagian) {
            $targetPerOrang = Karyawans::where('bagian', $row->bagian)
                ->join(
                    'target_jam_datamaster',
                    'karyawans.klinis_non_klinis',
                    '=',
                    'target_jam_datamaster.kategori'
                )
                ->avg('target_jam_datamaster.target_jam') ?? 0;

            $targetTotal = $row->total_karyawan * $targetPerOrang * 12;
            $aktual = $aktualPerBagian[$row->bagian] ?? 0;

            return [
                'kategori' => $row->bagian,
                'totalKaryawan' => $row->total_karyawan,
                'targetPerOrang' => round($targetPerOrang, 2),
                'totalTargetJam' => $targetTotal,
                'aktualJam' => (float) $aktual,
                'persentase' => $targetTotal > 0
                    ? round(($aktual / $targetTotal) * 100, 2)
                    : 0,
            ];
        })->sortByDesc('persentase')->values();

        $targetAll = $dataFinal->sum('totalTargetJam');
        $rekapJam = RekapJamDiklat::where('tahun', $year)->sum('total_jam');

        // === Metrik Tambahan ===
        $persentaseOverall = $targetAll > 0 ? round(($rekapJam / $targetAll) * 100, 2) : 0;
        $bulanSekarang = now()->month;
        $namaBulan = now()->locale('id')->monthName;
        $expectedProgress = round(($bulanSekarang / 12) * 100, 1);
        $paceStatus = $persentaseOverall >= $expectedProgress ? 'on_track' : 'behind';

        $bagianTercapai = $dataFinal->filter(fn($item) => $item['persentase'] >= 100)->count();
        $bagianBelumTercapai = $dataFinal->filter(fn($item) => $item['persentase'] < 100)->count();
        $rataRataPencapaian = $dataFinal->count() > 0 ? round($dataFinal->avg('persentase'), 2) : 0;

        $topPerformer = $dataFinal->isNotEmpty() ? $dataFinal->first() : null;
        $lowestPerformer = $dataFinal->isNotEmpty() ? $dataFinal->last() : null;

        // === DATA TREN BULANAN (BARU) ===
        $trenBulananRaw = RekapJamDiklat::where('tahun', $year)
            ->select('bulan')
            ->selectRaw('SUM(total_jam) as total_aktual')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_aktual', 'bulan');

        // Hitung target kumulatif per bulan (asumsi target merata per bulan)
        $targetPerBulan = $targetAll / 12;
        $trenBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            // Jika bulan belum lewat, target tetap ditampilkan tapi aktual 0
            $aktualBulan = $trenBulananRaw[$i] ?? 0;
            $targetKumulatif = round($targetPerBulan * $i);
            $aktualKumulatif = 0;

            // Hitung aktual kumulatif sampai bulan ke-i
            for ($j = 1; $j <= $i; $j++) {
                $aktualKumulatif += $trenBulananRaw[$j] ?? 0;
            }

            $trenBulanan[] = [
                'bulan' => $i,
                'namaBulan' => \Carbon\Carbon::create()->month($i)->locale('id')->monthName,
                'aktualBulan' => (float) $aktualBulan,
                'targetKumulatif' => $targetKumulatif,
                'aktualKumulatif' => (float) $aktualKumulatif,
            ];
        }

        // === TOP 5 BAGIAN UNTUK GRAFIK PERBANDINGAN (BARU) ===
        $top5Bagian = $dataFinal->take(5)->pluck('kategori')->toArray();

        $trenPerTopBagian = [];
        if (count($top5Bagian) > 0) {
            $trenPerBagianRaw = Karyawans::whereIn('karyawans.bagian', $top5Bagian)
                ->join('rekap_jam_diklat', 'karyawans.nrp', '=', 'rekap_jam_diklat.nrp')
                ->where('rekap_jam_diklat.tahun', $year)
                ->select('karyawans.bagian', 'rekap_jam_diklat.bulan')
                ->selectRaw('SUM(rekap_jam_diklat.total_jam) as total_aktual')
                ->groupBy('karyawans.bagian', 'rekap_jam_diklat.bulan')
                ->get();

            foreach ($top5Bagian as $bagian) {
                $dataBulan = [];
                for ($i = 1; $i <= 12; $i++) {
                    $val = $trenPerBagianRaw->first(fn($q) => $q->bagian === $bagian && $q->bulan === $i);
                    $dataBulan[] = $val ? (float) $val->total_aktual : 0;
                }
                $trenPerTopBagian[] = [
                    'namaBagian' => $bagian,
                    'data' => $dataBulan
                ];
            }
        }

        // dd($trenBulanan);
        return Inertia::render('Dashboard', [
            'totalKaryawans' => $karyawan,
            'totalPerKategori' => $dataFinal,
            'totalJamDiklat' => [
                'tahun' => $year,
                'totalJamDiklat' => $rekapJam,
            ],
            'targetAll' => $targetAll,
            'persentaseOverall' => $persentaseOverall,
            'bulanSekarang' => $bulanSekarang,
            'namaBulan' => $namaBulan,
            'expectedProgress' => $expectedProgress,
            'paceStatus' => $paceStatus,
            'bagianTercapai' => $bagianTercapai,
            'bagianBelumTercapai' => $bagianBelumTercapai,
            'rataRataPencapaian' => $rataRataPencapaian,
            'topPerformer' => $topPerformer,
            'lowestPerformer' => $lowestPerformer,
            'trenBulanan' => $trenBulanan, // Dikirim ke chart
            'trenPerTopBagian' => $trenPerTopBagian, // Dikirim ke chart
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function dashboardUser()
    {
        // Ambil data karyawan yang login
        $user = auth()->user();
        $karyawan = Karyawans::where('nrp', $user->nrp)->firstOrFail();

        $nrp = $karyawan->nrp;
        $kategori = $karyawan->klinis_non_klinis;
        $yearNow = now()->year;
        $monthNow = now()->month;
        $today = Carbon::today()->toDateString();

        // ==========================================
        // 1. TOTAL JAM DIKLAT TAHUNAN (REAL-TIME)
        // ==========================================
        $jamEksternalThn = DiklatEksternal::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->sum('jam_diklat');
        $jamHLCThn = HLCManajement::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->sum('jam_diklat');
        $jamMandiriThn = DiklatKaryawan::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->sum('jam_diklat');

        // Diklat Internal Tahunan
        $periodeIdsLewatThn = \App\Models\PeriodeUtama::whereYear('tanggal', $yearNow)->whereDate('tanggal', '<=', now())->pluck('id');
        $pesertaInternalThn = PeriodeBagianDetailInternal::where('nrp', $nrp)->whereIn('periode_id', $periodeIdsLewatThn)->with(['periode.aksi'])->get();

        $jamInternalThn = 0;
        foreach ($pesertaInternalThn as $peserta) {
            if ($peserta->periode && $peserta->periode->aksi) {
                $jamInternalThn += ($peserta->periode->aksi->jam_diklat ?? 0);
            }
        }

        $totalJamFinal = $jamEksternalThn + $jamHLCThn + $jamMandiriThn + $jamInternalThn;

        // ==========================================
        // 2. TOTAL JAM DIKLAT BULANAN (REAL-TIME)
        // ==========================================
        $jamEksternalBln = DiklatEksternal::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $monthNow)->where('status', 'approved')->sum('jam_diklat');
        $jamHLCBln = HLCManajement::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $monthNow)->where('status', 'approved')->sum('jam_diklat');
        $jamMandiriBln = DiklatKaryawan::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $monthNow)->where('status', 'approved')->sum('jam_diklat');

        // Diklat Internal Bulanan
        $periodeIdsLewatBln = \App\Models\PeriodeUtama::whereYear('tanggal', $yearNow)->whereMonth('tanggal', $monthNow)->whereDate('tanggal', '<=', now())->pluck('id');
        $pesertaInternalBln = PeriodeBagianDetailInternal::where('nrp', $nrp)->whereIn('periode_id', $periodeIdsLewatBln)->with(['periode.aksi'])->get();

        $jamInternalBln = 0;
        foreach ($pesertaInternalBln as $peserta) {
            if ($peserta->periode && $peserta->periode->aksi) {
                $jamInternalBln += ($peserta->periode->aksi->jam_diklat ?? 0);
            }
        }

        $totalJamFinalBulanan = $jamEksternalBln + $jamHLCBln + $jamMandiriBln + $jamInternalBln;

        // ==========================================
        // 3. TARGET KATEGORI & PROMOSI KUMULATIF 6 BULAN
        // ==========================================

        // Ambil target tahunan dari database (tetap di-load untuk data statistik dasar)
        $targetData = TargetJamModels::where('kategori', $kategori)->first();
        $targetJam = $targetData ? $targetData->target_jam : 0;

        // Hardcode target per BULAN berdasarkan kategori login
        $targetBulanan = match (strtoupper($kategori)) {
            'KLINIS' => 20.0,
            'NON KLINIS' => 12.5,
            'MANAJERIAL KLINIS' => 15.0,
            'MANAJERIAL NON KLINIS' => 15.0,
            default => 0.0
        };

        // Target Akumulasi penuh untuk blok 6 Bulan (Semesteran)
        $targetJam6Bulan = $targetBulanan * 6; // Contoh Manajerial Klinis: 15 * 6 = 90 Jam

        // Tentukan rentang bulan semester berjalan saat ini (Jan-Jun atau Jul-Des)
        $startMonth = $monthNow <= 6 ? 1 : 7;
        $endMonth = $monthNow <= 6 ? 6 : 12;

        // Hitung TOTAL JAM REALISASI khusus dalam rentang 6 bulan semester berjalan ini
        $jamEksternal6Bln = DiklatEksternal::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->whereBetween(\DB::raw('EXTRACT(MONTH FROM tanggal_mulai)'), [$startMonth, $endMonth])
            ->where('status', 'approved')
            ->sum('jam_diklat');

        $jamHLC6Bln = HLCManajement::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->whereBetween(\DB::raw('EXTRACT(MONTH FROM tanggal_mulai)'), [$startMonth, $endMonth])
            ->where('status', 'approved')
            ->sum('jam_diklat');

        $jamMandiri6Bln = DiklatKaryawan::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->whereBetween(\DB::raw('EXTRACT(MONTH FROM tanggal_mulai)'), [$startMonth, $endMonth])
            ->where('status', 'approved')
            ->sum('jam_diklat');

        $periodeIds6Bln = \App\Models\PeriodeUtama::whereYear('tanggal', $yearNow)
            ->whereBetween(\DB::raw('EXTRACT(MONTH FROM tanggal)'), [$startMonth, $endMonth])
            ->whereDate('tanggal', '<=', now())
            ->pluck('id');

        $pesertaInternal6Bln = PeriodeBagianDetailInternal::where('nrp', $nrp)
            ->whereIn('periode_id', $periodeIds6Bln)
            ->with(['periode.aksi'])
            ->get();

        $jamInternal6Bln = 0;
        foreach ($pesertaInternal6Bln as $peserta) {
            $jamInternal6Bln += ($peserta->periode->aksi->jam_diklat ?? 0);
        }

        // Gabungan total jam semester berjalan
        $totalJamSemesterIni = $jamEksternal6Bln + $jamHLC6Bln + $jamMandiri6Bln + $jamInternal6Bln;

        // Hitung konsistensi pemenuhan target bulanan individu untuk validasi status promosi
        $bulanTerpujiCount = 0;
        $bulanHarusLolos = 0;

        for ($m = $startMonth; $m <= $endMonth; $m++) {
            if ($m <= $monthNow) {
                $bulanHarusLolos++;

                $eksternalM = DiklatEksternal::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $m)->where('status', 'approved')->sum('jam_diklat');
                $hlcM = HLCManajement::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $m)->where('status', 'approved')->sum('jam_diklat');
                $mandiriM = DiklatKaryawan::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->whereMonth('tanggal_mulai', $m)->where('status', 'approved')->sum('jam_diklat');

                $periodeIdsM = \App\Models\PeriodeUtama::whereYear('tanggal', $yearNow)->whereMonth('tanggal', $m)->whereDate('tanggal', '<=', now())->pluck('id');
                $pesertaInternalM = PeriodeBagianDetailInternal::where('nrp', $nrp)->whereIn('periode_id', $periodeIdsM)->with(['periode.aksi'])->get();

                $internalM = 0;
                foreach ($pesertaInternalM as $peserta) {
                    $internalM += ($peserta->periode->aksi->jam_diklat ?? 0);
                }

                if (($eksternalM + $hlcM + $mandiriM + $internalM) >= $targetBulanan) {
                    $bulanTerpujiCount++;
                }
            }
        }

        // Persentase Progress Bar menuju target 6 bulan penuh (misal mencapai 90 Jam)
        $persentasePromosi = $targetJam6Bulan > 0 ? round(($totalJamSemesterIni / $targetJam6Bulan) * 100, 2) : 0;

        // Karyawan lulus promosi jika jam kumulatif semester tercapai DAN konsisten lulus di tiap bulan berjalan
        $promosi = ($totalJamSemesterIni >= $targetJam6Bulan);
        // && ($bulanTerpujiCount === $bulanHarusLolos);

        if ($promosi) {
            $pesanPromosi = "Memenuhi syarat promosi. Akumulasi semester ini ({$totalJamSemesterIni} Jam) telah memenuhi target minimal {$targetJam6Bulan} Jam.";
        } else {
            $pesanPromosi = "Belum memenuhi syarat promosi. Pencapaian Anda baru {$totalJamSemesterIni} Jam dari target {$targetJam6Bulan} Jam semester ini.";
        }

        
        $persentaseTahunan = $targetJam > 0 ? round(($totalJamFinal / $targetJam) * 100, 2) : 0;
        $persentaseBulananRealtime = $targetBulanan > 0 ? round(($totalJamFinalBulanan / $targetBulanan) * 100, 2) : 0;

        // ==========================================
        // 4. STATISTIK PER JENIS DIKLAT (TAHUNAN)
        // ==========================================
        $statsPerTipe = [
            [
                'tipe' => 'Eksternal',
                'total_jam' => $jamEksternalThn,
                'count' => DiklatEksternal::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->count(),
                'warna' => 'bg-emerald-500',
                'icon' => 'globe'
            ],
            [
                'tipe' => 'HLC',
                'total_jam' => $jamHLCThn,
                'count' => HLCManajement::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->count(),
                'warna' => 'bg-violet-500',
                'icon' => 'building'
            ],
            [
                'tipe' => 'Mandiri',
                'total_jam' => $jamMandiriThn,
                'count' => DiklatKaryawan::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'approved')->count(),
                'warna' => 'bg-blue-500',
                'icon' => 'user'
            ],
            [
                'tipe' => 'Internal',
                'total_jam' => $jamInternalThn,
                'count' => $pesertaInternalThn->count(),
                'warna' => 'bg-amber-500',
                'icon' => 'briefcase'
            ]
        ];

        // ==========================================
        // 5. JADWAL PENDING & MENDATANG
        // ==========================================
        $pendingEksternal = DiklatEksternal::where('nrp', $nrp)->where('status', 'menunggu_persetujuan')->get(['id', 'dokumen', 'tanggal_mulai', 'tanggal_selesai', 'penyelenggara']);
        $pendingHLC = HLCManajement::where('nrp', $nrp)->where('status', 'menunggu_persetujuan')->get(['id', 'nama_diklat', 'dokumen', 'tanggal_mulai', 'tanggal_selesai', 'penyelenggara']);

        $allPending = $pendingEksternal->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'penyelenggara' => $item->penyelenggara ?? '-',
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'dokumen' => $item->dokumen,
                'tipe' => 'Eksternal',
            ];
        })->concat($pendingHLC->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'penyelenggara' => $item->penyelenggara ?? '-',
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'dokumen' => $item->dokumen,
                'tipe' => 'HLC',
            ];
        }))->filter(function ($item) {
            return Carbon::parse($item['tanggal_selesai'])->endOfDay()->greaterThanOrEqualTo(now());
        })->sortBy('tanggal_mulai')->values();

        $periodeIdsMendatang = \App\Models\PeriodeUtama::whereDate('tanggal', '>=', now())->pluck('id');

        $jadwalInternal = PeriodeBagianDetailInternal::where('nrp', $nrp)
            ->whereIn('periode_id', $periodeIdsMendatang)
            ->with(['periode.detailProgram', 'periode.aksi'])
            ->get()
            ->map(function ($item) {
                $namaDiklat = $item->periode->detailProgram->nama_diklat ?? 'Internal Training';
                return [
                    'id' => $item->id,
                    'nama_diklat' => $namaDiklat,
                    'tanggal' => $item->periode->tanggal,
                    'jam_diklat' => ($item->periode->aksi->jam_diklat ?? 0),
                    'tempat' => $item->periode->tempat,
                    'tipe' => 'Internal',
                    'status' => 'Terjadwal',
                ];
            });

        $allEksternal = DiklatEksternal::where('nrp', $nrp)->whereDate('tanggal_selesai', '>=', now())->where('status', 'Setuju')->with([
            'kehadiran' => function ($query) use ($today) {
                $query->where('tanggal', $today); }
        ])->get();

        $jadwalEksternal = $allEksternal->filter(function ($item) use ($today) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            $nextDate = $lastAbsen ? Carbon::parse($lastAbsen)->addDay()->toDateString() : $item->tanggal_mulai;
            return $nextDate >= $today && $nextDate <= $item->tanggal_selesai;
        })->map(function ($item) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            $displayDate = $lastAbsen ? Carbon::parse($lastAbsen)->addDay()->toDateString() : $item->tanggal_mulai;
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'tanggal' => $displayDate,
                'jam_diklat' => $item->jam_diklat,
                'tempat' => $item->penyelenggara,
                'tipe' => 'Eksternal',
                'status' => $item->status,
            ];
        });

        $allHLC = HLCManajement::where('nrp', $nrp)->whereDate('tanggal_selesai', '>=', now())->where('status', 'Setuju')->with([
            'kehadiran' => function ($query) use ($today) {
                $query->where('tanggal', $today); }
        ])->get();

        $jadwalHLC = $allHLC->filter(function ($item) use ($today) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            $nextDate = $lastAbsen ? Carbon::parse($lastAbsen)->addDay()->toDateString() : $item->tanggal_mulai;
            return $nextDate >= $today && $nextDate <= $item->tanggal_selesai;
        })->map(function ($item) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            $displayDate = $lastAbsen ? Carbon::parse($lastAbsen)->addDay()->toDateString() : $item->tanggal_mulai;
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'tanggal' => $displayDate,
                'jam_diklat' => $item->jam_diklat,
                'tempat' => $item->penyelenggara,
                'tipe' => 'HLC',
                'status' => $item->status,
            ];
        });

        $allJadwal = $jadwalInternal->concat($jadwalEksternal)->concat($jadwalHLC)->sortBy('tanggal')->take(3)->values();

        // ==========================================
        // 6. CHART DATA
        // ==========================================
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $baseBulan = array_fill(1, 12, 0);

        $mapBulanan = function ($query) use ($baseBulan, $yearNow) {
            $data = $query->selectRaw('EXTRACT(MONTH FROM tanggal_mulai)::integer as bulan, SUM(jam_diklat) as total')
                ->whereYear('tanggal_mulai', $yearNow)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            $result = [];
            foreach ($baseBulan as $bulan => $val) {
                $result[] = $data[$bulan] ?? 0;
            }
            return $result;
        };

        $chartData = [
            'labels' => $months,
            'datasets' => [
                ['label' => 'Eksternal', 'data' => $mapBulanan(DiklatEksternal::where('nrp', $nrp)->where('status', 'approved')), 'borderColor' => '#10b981', 'tension' => 0.4],
                ['label' => 'HLC', 'data' => $mapBulanan(HLCManajement::where('nrp', $nrp)->where('status', 'approved')), 'borderColor' => '#8b5cf6', 'tension' => 0.4],
                ['label' => 'Mandiri', 'data' => $mapBulanan(DiklatKaryawan::where('nrp', $nrp)->where('status', 'approved')), 'borderColor' => '#3b82f6', 'tension' => 0.4],
            ]
        ];

        $internalPerBulan = $baseBulan;
        foreach ($pesertaInternalThn as $peserta) {
            if ($peserta->periode && $peserta->periode->aksi && $peserta->periode->tanggal) {
                $bulan = (int) date('n', strtotime($peserta->periode->tanggal));
                $internalPerBulan[$bulan] += ($peserta->periode->aksi->jam_diklat ?? 0);
            }
        }
        $chartData['datasets'][] = ['label' => 'Internal', 'data' => array_values($internalPerBulan), 'borderColor' => '#f59e0b', 'tension' => 0.4];

        // ==========================================
        // 7. RETURN INTERTIA DATA VIEW
        // ==========================================
        return Inertia::render('DashboardUser', [
            'totalJam' => $totalJamFinal,
            'totalJamBulanan' => $totalJamFinalBulanan,
            'targetJam' => $targetJam,
            'targetBulanan' => $targetBulanan,
            'targetJam6Bulan' => $targetJam6Bulan,
            'totalJamSemesterIni' => $totalJamSemesterIni,
            'persentase' => $persentaseTahunan,
            'persentaseBulanan' => $persentaseBulananRealtime,
            'persentasePromosi' => $persentasePromosi,
            'statsPerTipe' => $statsPerTipe,
            'pendingDiklat' => $allPending,
            'jadwalInternal' => $allJadwal,
            'namaKaryawan' => $karyawan->nama_karyawan,
            'kategori' => $kategori,
            'chartData' => $chartData,
            'promosi' => $promosi,
            'pesanPromosi' => $pesanPromosi,
            'bulanTerpujiCount' => $bulanTerpujiCount,
            'bulanHarusLolos' => $bulanHarusLolos,
        ]);
    }
}