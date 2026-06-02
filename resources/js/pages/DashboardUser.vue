<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

// --- IMPORT CHART.JS ---
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
);

// Interface Data
interface StatPerTipe {
    tipe: string;
    total_jam: number;
    count: number;
    warna: string;
    icon: string;
}

interface JadwalInternal {
    id: number;
    nama_diklat: string;
    tanggal: string;
    jam_diklat: number;
    tempat: string;
    tipe: 'Internal' | 'Eksternal' | 'HLC';
    status: string;
}

interface PendingDiklat {
    id: number;
    nama_diklat: string;
    tanggal_mulai: string;
    penyelenggara: string;
    tipe: 'Eksternal' | 'HLC';
    dokumen?: string | null;
}

interface ChartData {
    labels: string[];
    datasets: {
        label: string;
        data: number[];
        borderColor: string;
        tension: number;
    }[];
}

// Definisi Props Resmi (Sinkron Total dengan Controller)
const props = defineProps<{
    totalJam: number;
    totalJamBulanan: number;
    targetJam: number;
    targetBulanan: number;
    targetJam6Bulan: number;
    totalJamSemesterIni: number;
    persentase: number;
    persentaseBulanan: number;
    persentasePromosi: number;
    statsPerTipe: StatPerTipe[];
    pendingDiklat: PendingDiklat[];
    jadwalInternal: JadwalInternal[];
    namaKaryawan: string;
    kategori: string;
    chartData: ChartData;
    promosi: boolean;
    pesanPromosi: string;
    bulanTerpujiCount: number;
    bulanHarusLolos: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
];

// HELPER FUNCTIONS
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getIconDiklat = (tipe: string) => {
    if (tipe === 'HLC') {
        return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>`;
    }
    return `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
    </svg>`;
};

const getTipeClass = (tipe: string) => {
    if (tipe === 'HLC') return 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300';
    return 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300';
};

// Logic Progress Ring Tahunan
const RING_RADIUS = 40;
const RING_CIRCUMFERENCE = 2 * Math.PI * RING_RADIUS;
const ringOffset = computed(() => {
    const pct = Math.min(props.persentase, 100);
    return RING_CIRCUMFERENCE * (1 - pct / 100);
});

const statusWarna = computed(() => {
    if (props.persentase >= 100) return 'text-emerald-600 dark:text-emerald-400 stroke-emerald-500';
    if (props.persentase >= 75) return 'text-blue-600 dark:text-blue-400 stroke-blue-500';
    if (props.persentase >= 50) return 'text-amber-600 dark:text-amber-400 stroke-amber-500';
    return 'text-red-600 dark:text-red-400 stroke-red-500';
});

// Logic Progress Ring Bulanan
const RING_RADIUS_BULANAN = 40;
const RING_CIRCUMFERENCE_BULANAN = 2 * Math.PI * RING_RADIUS_BULANAN;
const ringOffsetBulanan = computed(() => {
    const pct = Math.min(props.persentaseBulanan, 100);
    return RING_CIRCUMFERENCE_BULANAN * (1 - pct / 100);
});

const statusWarnaBulanan = computed(() => {
    if (props.persentaseBulanan >= 100) return 'text-emerald-600 dark:text-emerald-400 stroke-emerald-500';
    if (props.persentaseBulanan >= 75) return 'text-blue-600 dark:text-blue-400 stroke-blue-500';
    if (props.persentaseBulanan >= 50) return 'text-amber-600 dark:text-amber-400 stroke-amber-500';
    return 'text-red-600 dark:text-red-400 stroke-red-500';
});

// Hitung total kegiatan aktif
const totalKegiatanAktif = computed(() => {
    const totalDiklatSelesai = props.statsPerTipe.reduce((acc, curr) => acc + curr.count, 0);
    return totalDiklatSelesai + props.pendingDiklat.length;
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8' },
            title: { display: true, text: 'Total Jam', color: '#94a3b8' },
        },
        x: {
            grid: { display: false },
            ticks: { color: '#94a3b8' },
        },
    },
    plugins: {
        legend: {
            position: 'top' as const,
            labels: { color: '#64748b', usePointStyle: true, padding: 20 },
        },
        tooltip: {
            mode: 'index' as const,
            intersect: false,
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: function (context: any) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    if (context.parsed.y !== null) label += context.parsed.y + ' Jam';
                    return label;
                },
            },
        },
    },
    elements: {
        line: { tension: 0.4 },
    },
};
</script>

<template>
    <Head title="Dashboard Karyawan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Selamat Datang, {{ namaKaryawan }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Kategori: {{ kategori }} &bull; Pantau perkembangan kompetensi Anda di sini.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400">
                                Pencapaian Tahun Ini
                            </p>
                            <p class="mt-2 text-2xl leading-tight font-extrabold text-slate-900 dark:text-white">
                                {{ totalJam }}
                            </p>
                            <p class="mt-1 text-sm font-semibold" :class="statusWarna">
                                {{ Math.round(persentase) }}%
                            </p>
                        </div>
                        <div class="relative shrink-0">
                            <svg viewBox="0 0 100 100" class="h-16 w-16 -rotate-90 transform">
                                <circle cx="50" cy="50" :r="RING_RADIUS" fill="none" stroke-width="8" class="stroke-slate-100 dark:stroke-slate-800" />
                                <circle cx="50" cy="50" :r="RING_RADIUS" fill="none" stroke-width="8" stroke-linecap="round" :class="[statusWarna, 'transition-all duration-1000 ease-out']" :stroke-dasharray="RING_CIRCUMFERENCE" :stroke-dashoffset="ringOffset" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400">
                                Pencapaian Bulan Ini
                            </p>
                            <p class="mt-2 text-3xl leading-tight font-extrabold text-slate-900 dark:text-white">
                                {{ totalJamBulanan }}
                                <span class="block text-xs font-normal text-slate-400">/ {{ targetBulanan }} Jam</span>
                            </p>
                            <p class="mt-2 text-sm font-semibold" :class="statusWarnaBulanan">
                                {{ Math.round(persentaseBulanan) }}%
                            </p>
                        </div>
                        <div class="relative shrink-0">
                            <svg viewBox="0 0 100 100" class="h-16 w-16 -rotate-90 transform">
                                <circle cx="50" cy="50" :r="RING_RADIUS_BULANAN" fill="none" stroke-width="8" class="stroke-slate-100 dark:stroke-slate-800" />
                                <circle cx="50" cy="50" :r="RING_RADIUS_BULANAN" fill="none" stroke-width="8" stroke-linecap="round" :class="[statusWarnaBulanan, 'transition-all duration-1000 ease-out']" :stroke-dasharray="RING_CIRCUMFERENCE_BULANAN" :stroke-dashoffset="ringOffsetBulanan" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900/50 dark:bg-amber-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-medium tracking-wider text-amber-700 uppercase dark:text-amber-400">
                                Menunggu Persetujuan
                            </p>
                            <p class="mt-2 text-3xl font-extrabold text-amber-800 dark:text-amber-300">
                                {{ pendingDiklat.length }}
                            </p>
                            <p class="mt-1 text-xs text-amber-600 dark:text-amber-500">Diklat</p>
                        </div>
                        <div class="rounded-xl bg-amber-500 p-3 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400">
                                Jadwal Terdekat
                            </p>
                            <p v-if="jadwalInternal.length > 0" class="mt-2 text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                {{ formatDate(jadwalInternal[0].tanggal) }}
                            </p>
                            <p v-else class="mt-2 text-sm text-slate-400 italic">Tidak ada jadwal dekat.</p>
                            <p class="mt-1 text-xs text-slate-400">Diklat Internal</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400">
                                Total Kegiatan
                            </p>
                            <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">
                                {{ totalKegiatanAktif }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">Selesai & Menunggu</p>
                        </div>
                        <div class="rounded-xl bg-violet-50 p-3 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-2xl border p-6 shadow-sm transition-all duration-700 ease-out"
                :class="[
                    promosi
                        ? 'border-transparent bg-gradient-to-r from-violet-600 via-fuchsia-600 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'border-slate-200 bg-white text-slate-900 dark:border-slate-800 dark:bg-slate-900 dark:text-white',
                ]"
            >
                <div v-if="promosi" class="absolute -top-10 -right-10 h-48 w-48 animate-pulse rounded-full bg-white/20 blur-3xl"></div>
                <div v-if="promosi" class="absolute -bottom-10 -left-10 h-40 w-40 animate-pulse rounded-full bg-white/20 blur-3xl" style="animation-delay: 1s"></div>

                <div class="relative z-10 flex flex-col gap-5">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full transition-colors" :class="promosi ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8" :class="promosi ? 'animate-bounce text-yellow-300' : 'text-slate-400'">
                                    <path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 0 0-.584.859 6.753 6.753 0 0 0 6.138 5.6 27.355 27.355 0 0 0 1.052.148c.11.116.225.231.346.342l.534.489a2.766 2.766 0 0 0 2.457 0l.534-.489a27.18 27.18 0 0 0 1.398-.49 6.753 6.753 0 0 0 6.137-5.6.75.75 0 0 0-.584-.86 48.243 48.243 0 0 0-3.072-.543v-.858a.75.75 0 0 0-.75-.75h-9a.75.75 0 0 0-.75.75Zm4.364 12.879a.75.75 0 0 0-1.05-.039l-2.062 1.879a.75.75 0 0 0-.256.62l.582 4.075a.75.75 0 0 0 1.25.56l2.355-2.064a.75.75 0 0 0 .185-.828l-.75-2.616a.75.75 0 0 0-.254-.588Zm3.94 0a.75.75 0 0 1 1.05-.039l2.062 1.879a.75.75 0 0 1 .256.62l-.582 4.075a.75.75 0 0 1-1.25.56l-2.355-2.064a.75.75 0 0 1-.185-.828l.75-2.616a.75.75 0 0 1 .254-.588Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold tracking-tight">Capaian Promosi Kategori (6 Bulan)</h2>
                                <p class="mt-0.5 text-sm font-medium" :class="promosi ? 'text-white/90' : 'text-slate-500 dark:text-slate-400'">
                                    {{ pesanPromosi }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-extrabold tracking-tight">
                                {{ Math.min(Math.round(persentasePromosi), 100) }}%
                            </span>
                            <p class="text-[10px] font-medium tracking-wider uppercase opacity-75">Progress Promosi</p>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="h-3 w-full rounded-full overflow-hidden" :class="promosi ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800'">
                            <div 
                                class="h-full rounded-full transition-all duration-1000 ease-out"
                                :class="promosi ? 'bg-white shadow-[0_0_12px_rgba(255,255,255,0.6)]' : 'bg-fuchsia-600 dark:bg-fuchsia-500'"
                                :style="{ width: `${Math.min(persentasePromosi, 100)}%` }"
                            ></div>
                        </div>
                        <div class="mt-2 flex justify-between text-xs font-medium" :class="promosi ? 'text-white/80' : 'text-slate-400 dark:text-slate-500'">
                            <span>Pencapaian Semester: <b>{{ totalJamSemesterIni }} Jam</b></span>
                            <span>Target Kategori (6 Bulan): <b>{{ targetJam6Bulan }} Jam</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Tren Jam Diklat Bulanan ({{ new Date().getFullYear() }})
                    </h2>
                </div>
                <div class="relative h-72 w-full">
                    <Line :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 text-sm font-bold tracking-wider text-slate-900 uppercase dark:text-white">
                    Rincian Perkembangan ({{ new Date().getFullYear() }})
                </h2>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div v-for="(stat, index) in statsPerTipe" :key="index" class="flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                        <div class="flex items-center justify-between">
                            <div class="rounded-lg p-2 text-white" :class="stat.warna">
                                <svg v-if="stat.icon === 'globe'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else-if="stat.icon === 'building'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <svg v-else-if="stat.icon === 'user'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-400">{{ stat.count }}x</span>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ stat.total_jam }}</p>
                            <p class="text-xs font-medium text-slate-500 uppercase">Jam {{ stat.tipe }}</p>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full rounded-full transition-all duration-1000" :class="stat.warna" :style="{ width: (totalJam > 0 ? (stat.total_jam / totalJam) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Jadwal Mendatang</h2>
                        <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                    </div>
                    <div v-if="jadwalInternal.length > 0" class="flex flex-col gap-3">
                        <div v-for="item in jadwalInternal" :key="item.id + '-' + item.tipe" class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-800">
                            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <span class="text-xs font-bold uppercase">{{ new Date(item.tanggal).toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                                <span class="text-lg leading-none font-bold">{{ new Date(item.tanggal).getDate() }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="mb-1 flex items-center gap-2">
                                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ item.nama_diklat }}</h3>
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-bold tracking-wide uppercase" :class="{ 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300': item.tipe === 'Internal', 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300': item.tipe === 'Eksternal', 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300': item.tipe === 'HLC' }">
                                        {{ item.tipe }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">{{ item.tempat }} &bull; {{ item.jam_diklat }} JP</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="{ 'bg-slate-100 text-slate-600': item.status === 'Terjadwal' || item.status === 'Disetujui' || item.status === 'Setuju', 'bg-amber-100 text-amber-700': item.status === 'Menunggu', 'bg-red-100 text-red-700': item.status === 'Ditolak' }">
                                    {{ item.status }}
                                </span>
                                <button class="rounded-lg bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300">Detail</button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 dark:border-slate-700 dark:bg-slate-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">Tidak ada jadwal mendatang.</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Status Pengajuan Diklat</h2>
                        <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                    </div>
                    <div v-if="pendingDiklat.length > 0" class="flex flex-col gap-3">
                        <div v-for="item in pendingDiklat" :key="item.id + '-' + item.tipe" class="flex items-center gap-4 rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm transition-all hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-900/20 dark:hover:bg-amber-900/30">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400">
                                <div v-html="getIconDiklat(item.tipe)"></div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ item.nama_diklat }}</h3>
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase" :class="getTipeClass(item.tipe)">{{ item.tipe }}</span>
                                </div>
                                <p class="text-xs text-slate-500">{{ item.penyelenggara }} &bull; Mulai {{ formatDate(item.tanggal_mulai) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-amber-200 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Menunggu</span>
                                <a v-if="item.dokumen" :href="`/storage/${item.dokumen}`" target="_blank" class="rounded-lg border border-slate-200 bg-white p-1.5 text-slate-500 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-blue-400" title="Lihat Dokumen">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 dark:border-slate-700 dark:bg-slate-800/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">Semua pengajuan sudah diproses.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>