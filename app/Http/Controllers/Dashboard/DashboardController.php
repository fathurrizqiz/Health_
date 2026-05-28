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

        // ==========================================
        // 1. TOTAL JAM DIKLAT (REAL-TIME CALCULATION)
        // ==========================================

        // a. Diklat Eksternal (Status Disetujui/Hadir)
        $jamEksternal = DiklatEksternal::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->where('status', 'approved') // Hanya yang disetujui admin
            ->sum('jam_diklat');

        // b. Diklat HLC (Status Hadir/Disetujui)
        // Asumsi: kolom status atau status_verifikasi yang menentukan sahnya
        $jamHLC = HLCManajement::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->where('status', 'approved') // Atau status_verifikasi == 'Disetujui'
            ->sum('jam_diklat');

        // c. Diklat Mandiri (Status Disetujui)
        $jamMandiri = DiklatKaryawan::where('nrp', $nrp)
            ->whereYear('tanggal_mulai', $yearNow)
            ->where('status', 'approved')
            ->sum('jam_diklat');

        // d. Diklat Internal
        // Logic: Cari di tabel PeriodeUtama (jadwal) yang sudah lewat tanggalnya, 
        // lalu cek apakah user ada di peserta (PeriodeBagianDetailInternal).
        // Ambil jam_diklat dari relasi aksi/program.

        // Ambil semua ID periode yang tanggalnya TIDAK di masa depan (sudah lewat atau hari ini)
        $periodeIdsLewat = \App\Models\PeriodeUtama::whereDate('tanggal', '<=', now())
            ->pluck('id');

        // Cek kepesertaan
        $pesertaInternal = PeriodeBagianDetailInternal::where('nrp', $nrp)
            ->whereIn('periode_id', $periodeIdsLewat)
            ->with(['periode.aksi']) // Load jam dari setting aksi
            ->get();

        $jamInternal = 0;
        foreach ($pesertaInternal as $peserta) {
            // Tambah jam jika ada data aksi
            if ($peserta->periode && $peserta->periode->aksi) {
                $jamInternal += ($peserta->periode->aksi->jam_diklat ?? 0);
            }
        }

        // Total Realtime
        $totalJamRealtime = $jamEksternal + $jamHLC + $jamMandiri + $jamInternal;

        // ==========================================
        // 2. STATISTIK REKAP (DARI TABEL REKAP)
        // ==========================================

        $totalJamRekap = RekapJamDiklat::where('nrp', $nrp)
            ->where('tahun', $yearNow)
            ->sum('total_jam');

        // Gunakan total realtime jika rekap belum terupdate, atau gunakan rekap sesuai kebutuhan bisnis
        // Disini saya gunakan Realtime agar dashboard user selalu update terbaru
        $totalJamFinal = $totalJamRealtime;

        // ==========================================
        // 3. TARGET & PERSENTASE
        // ==========================================
        $targetData = TargetJamModels::where('kategori', $kategori)->first();
        $targetJam = $targetData ? $targetData->target_jam : 0;

        $persentase = $targetJam > 0 ? round(($totalJamFinal / $targetJam) * 100, 2) : 0;

        // ==========================================
        // 4. STATISTIK PER JENIS DIKLAT
        // ==========================================

        // Hitung masing-masing tipe untuk visualisasi grafik/pie
        $statsPerTipe = [
            [
                'tipe' => 'Eksternal',
                'total_jam' => $jamEksternal,
                'count' => DiklatEksternal::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status_verifikasi', 'Disetujui')->count(),
                'warna' => 'bg-emerald-500', // Hijau
                'icon' => 'globe'
            ],
            [
                'tipe' => 'HLC',
                'total_jam' => $jamHLC,
                'count' => HLCManajement::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'Hadir')->count(),
                'warna' => 'bg-violet-500', // Ungu
                'icon' => 'building'
            ],
            [
                'tipe' => 'Mandiri',
                'total_jam' => $jamMandiri,
                'count' => DiklatKaryawan::where('nrp', $nrp)->whereYear('tanggal_mulai', $yearNow)->where('status', 'Disetujui')->count(),
                'warna' => 'bg-blue-500', // Biru
                'icon' => 'user'
            ],
            [
                'tipe' => 'Internal',
                'total_jam' => $jamInternal,
                'count' => $pesertaInternal->count(), // Hitung berapa kali diundang
                'warna' => 'bg-amber-500', // Kuning/Orange
                'icon' => 'briefcase'
            ]
        ];

        // ==========================================
        // 5. DATA JADWAL & PENDING (TETAP SAMA)
        // ==========================================

        // 1. Ambil Data Eksternal
        $pendingEksternal = DiklatEksternal::where('nrp', $nrp)
            ->where('status', 'menunggu_persetujuan')
            ->get(['id', 'diklat', 'dokumen', 'tanggal_mulai', 'penyelenggara']);

        // 2. Ambil Data HLC
        $pendingHLC = HLCManajement::where('nrp', $nrp)
            ->where('status', 'menunggu_persetujuan')
            ->get(['id', 'nama_diklat', 'dokumen', 'tanggal_mulai', 'penyelenggara']);

        // 3. Gabungkan Map & Tambah Tipe
        $allPending = $pendingEksternal->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'penyelenggara' => $item->penyelenggara ?? '-',
                'tanggal_mulai' => $item->tanggal_mulai,
                'dokumen' => $item->dokumen,
                'tipe' => 'Eksternal', // Tipe untuk Vue
            ];
        })->concat($pendingHLC->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'penyelenggara' => $item->penyelenggara ?? '-',
                'tanggal_mulai' => $item->tanggal_mulai,
                'dokumen' => $item->dokumen,
                'tipe' => 'HLC', // Tipe untuk Vue
            ];
        }))->sortBy('tanggal_mulai')->values(); // Urutkan berdasarkan tanggal mulai

        // Jadwal Internal Mendatang
        $periodeIdsMendatang = \App\Models\PeriodeUtama::whereDate('tanggal', '>=', now())
            ->pluck('id');

        $periodeIdsMendatang = \App\Models\PeriodeUtama::whereDate('tanggal', '>=', now())
            ->pluck('id');

        $jadwalInternal = PeriodeBagianDetailInternal::where('nrp', $nrp)
            ->whereIn('periode_id', $periodeIdsMendatang)
            ->with(['periode.detailProgram', 'periode.aksi'])
            ->get()
            ->map(function ($item) {
                $namaDiklat = 'Internal Training';
                if ($item->periode && $item->periode->detailProgram) {
                    $namaDiklat = $item->periode->detailProgram->nama_diklat ?? $namaDiklat;
                }

                return [
                    'id' => $item->id,
                    'nama_diklat' => $namaDiklat,
                    'tanggal' => $item->periode->tanggal,
                    'jam_diklat' => ($item->periode->aksi->jam_diklat ?? 0),
                    'tempat' => $item->periode->tempat,
                    'tipe' => 'Internal', // Tandai sebagai Internal
                    'status' => 'Terjadwal', // Internal status default
                ];
            });

        // 2. Jadwal Eksternal Mendatang
        // Ambil tanggal hari ini
        $today = Carbon::today()->toDateString();

        // ==========================================
        // 2. Jadwal Eksternal Mendatang (Dengan Logika Absensi)
        // ==========================================
        $allEksternal = DiklatEksternal::where('nrp', $nrp)
            ->whereDate('tanggal_selesai', '>=', now()) // Cek jangkauan waktu masih valid
            ->where('status', 'Setuju')
            ->with([
                'kehadiran' => function ($query) use ($today) {
                    // Hanya load absensi hari ini
                    $query->where('tanggal', $today);
                }
            ])
            ->get();

        $jadwalEksternal = $allEksternal->filter(function ($item) use ($today) {
            // LOGIKA:
            // 1. Ambil tanggal mulai diklat.
            // 2. Jika hari ini LEBIH BESAR dari tanggal mulai, jadwalnya bergeser.
            // 3. Namun, kita harus cek apakah user ABSEN pada tanggal yang dimaksud.

            // Cari tanggal absen terakhir user untuk diklat ini
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');

            // Tentukan 'tanggal jadwal berikutnya'
            if ($lastAbsen) {
                // Kita gunakan Carbon untuk menambah 1 hari
                $nextDate = Carbon::parse($lastAbsen)->addDay()->toDateString();
            } else {
                // Jika belum absen sama sekali, jadwalnya adalah tanggal mulai diklat
                $nextDate = $item->tanggal_mulai;
            }

            // Agar tidak muncul tanggal kemarin yang sudah lewat:
            return $nextDate >= $today && $nextDate <= $item->tanggal_selesai;

        })->map(function ($item) use ($today) {
            // Ulangi logika penentuan tanggal untuk ditampilkan di view
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            if ($lastAbsen) {
                $displayDate = Carbon::parse($lastAbsen)->addDay()->toDateString();
            } else {
                $displayDate = $item->tanggal_mulai;
            }

            return [
                'id' => $item->id,
                'nama_diklat' => $item->nama_diklat,
                'tanggal' => $displayDate, // Tanggal dinamis (bisa 29 jika 28 sudah absen)
                'jam_diklat' => $item->jam_diklat,
                'tempat' => $item->penyelenggara,
                'tipe' => 'Eksternal',
                'status' => $item->status,
            ];
        });


        // ==========================================
        // 3. Jadwal HLC Mendatang (Sama Logikanya)
        // ==========================================
        $allHLC = HLCManajement::where('nrp', $nrp)
            ->whereDate('tanggal_selesai', '>=', now())
            ->where('status', 'Setuju')
            // ->where('status', '!=', 'Ditolak')
            ->with([
                'kehadiran' => function ($query) use ($today) {
                    $query->where('tanggal', $today);
                }
            ])
            ->get();

        $jadwalHLC = $allHLC->filter(function ($item) use ($today) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');

            if ($lastAbsen) {
                $nextDate = Carbon::parse($lastAbsen)->addDay()->toDateString();
            } else {
                $nextDate = $item->tanggal_mulai;
            }

            return $nextDate >= $today && $nextDate <= $item->tanggal_selesai;

        })->map(function ($item) use ($today) {
            $lastAbsen = $item->kehadiran->where('status', 'hadir')->max('tanggal');
            if ($lastAbsen) {
                $displayDate = Carbon::parse($lastAbsen)->addDay()->toDateString();
            } else {
                $displayDate = $item->tanggal_mulai;
            }

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

        // 4. Gabungkan, Urutkan, dan Batasi
        $allJadwal = $jadwalInternal->concat($jadwalEksternal)->concat($jadwalHLC)
            ->sortBy('tanggal') // Urutkan berdasarkan tanggal terdekat
            ->take(3) // Ambil 3 terdekat
            ->values();

        return Inertia::render('DashboardUser', [
            'totalJam' => $totalJamFinal,
            'targetJam' => $targetJam,
            'persentase' => $persentase,
            'statsPerTipe' => $statsPerTipe, // Data baru untuk breakdown statistik
            'pendingDiklat' => $allPending,
            'jadwalInternal' => $allJadwal,
            'namaKaryawan' => $karyawan->nama_karyawan,
            'kategori' => $kategori,
        ]);
    }
}