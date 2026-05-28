<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    tipe: 'Internal' | 'Eksternal' | 'HLC'; // Tambah ini
    status: string; // Tambah ini
}

interface PendingDiklat {
    id: number;
    nama_diklat: string;
    tanggal_mulai: string;
    penyelenggara: string;
    tipe: 'Eksternal' | 'HLC'; // Tipe baru
    dokumen?: string | null; // Optional, hanya untuk eksternal
}

// Definisi Props
const props = defineProps<{
    totalJam: number;
    targetJam: number;
    persentase: number;
    statsPerTipe: StatPerTipe[]; // Data breakdown baru
    pendingDiklat: PendingDiklat[];
    jadwalInternal: JadwalInternal[];
    namaKaryawan: string;
    kategori: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
];

// HELPER FUNCTIONS
// Helper format tanggal
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

// Helper untuk icon berdasarkan tipe
const getIconDiklat = (tipe: string) => {
    if (tipe === 'HLC') {
        return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>`;
    }
    // Default Eksternal (Globe)
    return `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
    </svg>`;
};

// Helper warna badge tipe
const getTipeClass = (tipe: string) => {
    if (tipe === 'HLC')
        return 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300';
    return 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300';
};

// Logic Progress Ring
const RING_RADIUS = 40;
const RING_CIRCUMFERENCE = 2 * Math.PI * RING_RADIUS;
const ringOffset = computed(() => {
    const pct = Math.min(props.persentase, 100);
    return RING_CIRCUMFERENCE * (1 - pct / 100);
});

// Helper Warna Progress
const statusWarna = computed(() => {
    if (props.persentase >= 100)
        return 'text-emerald-600 dark:text-emerald-400 stroke-emerald-500';
    if (props.persentase >= 75)
        return 'text-blue-600 dark:text-blue-400 stroke-blue-500';
    if (props.persentase >= 50)
        return 'text-amber-600 dark:text-amber-400 stroke-amber-500';
    return 'text-red-600 dark:text-red-400 stroke-red-500';
});

// Hitung total kegiatan aktif untuk statistik ke-4
const totalKegiatanAktif = computed(() => {
    // Menghitung total kegiatan dari breakdown (count) + pending eksternal
    const totalDiklatSelesai = props.statsPerTipe.reduce(
        (acc, curr) => acc + curr.count,
        0,
    );
    return totalDiklatSelesai + props.pendingDiklat.length;
});
</script>

<template>
    <Head title="Dashboard Karyawan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Selamat Datang, {{ namaKaryawan }} 
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Kategori: {{ kategori }} &bull; Pantau perkembangan
                    kompetensi Anda di sini.
                </p>
            </div>

            <!-- Statistik Utama -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- 1. Target & Progress (Ring) -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                Pencapaian Tahun Ini
                            </p>
                            <p
                                class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white"
                            >
                                {{ totalJam }}
                                <span class="text-sm font-medium text-slate-400"
                                    >/ {{ targetJam }} Jam</span
                                >
                            </p>
                            <p
                                class="mt-1 text-sm font-semibold"
                                :class="statusWarna"
                            >
                                {{ persentase }}%
                            </p>
                        </div>
                        <!-- Ring Chart Mini -->
                        <div class="relative shrink-0">
                            <svg
                                viewBox="0 0 100 100"
                                class="h-20 w-20 -rotate-90 transform"
                            >
                                <circle
                                    cx="50"
                                    cy="50"
                                    :r="RING_RADIUS"
                                    fill="none"
                                    stroke-width="8"
                                    class="stroke-slate-100 dark:stroke-slate-800"
                                />
                                <circle
                                    cx="50"
                                    cy="50"
                                    :r="RING_RADIUS"
                                    fill="none"
                                    stroke-width="8"
                                    stroke-linecap="round"
                                    :class="[
                                        statusWarna,
                                        'transition-all duration-1000 ease-out',
                                    ]"
                                    :stroke-dasharray="RING_CIRCUMFERENCE"
                                    :stroke-dashoffset="ringOffset"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- 2. Pending Approval -->
                <div
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-900/50 dark:bg-amber-900/20"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-amber-700 uppercase dark:text-amber-400"
                            >
                                Menunggu Persetujuan
                            </p>
                            <p
                                class="mt-2 text-3xl font-extrabold text-amber-800 dark:text-amber-300"
                            >
                                {{ pendingDiklat.length }}
                            </p>
                            <p
                                class="mt-1 text-xs text-amber-600 dark:text-amber-500"
                            >
                                Diklat Eksternal
                            </p>
                        </div>
                        <div class="rounded-xl bg-amber-500 p-3 text-white">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- 3. Upcoming Internal -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                Jadwal Terdekat
                            </p>
                            <p
                                v-if="jadwalInternal.length > 0"
                                class="mt-2 text-lg font-bold text-slate-900 dark:text-white"
                            >
                                {{ formatDate(jadwalInternal[0].tanggal) }}
                            </p>
                            <p
                                v-else
                                class="mt-2 text-sm text-slate-400 italic"
                            >
                                Tidak ada jadwal dekat.
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Diklat Internal
                            </p>
                        </div>
                        <div
                            class="rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- 4. Total Kegiatan (Revisi: Menggunakan computed totalKegiatanAktif) -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                Total Kegiatan
                            </p>
                            <p
                                class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white"
                            >
                                {{ totalKegiatanAktif }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Selesai & Menunggu
                            </p>
                        </div>
                        <div
                            class="rounded-xl bg-violet-50 p-3 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path
                                    fill-rule="evenodd"
                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Statistik Perkembangan (Breakdown) -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <h2
                    class="mb-4 text-sm font-bold tracking-wider text-slate-900 uppercase dark:text-white"
                >
                    Rincian Perkembangan ({{ new Date().getFullYear() }})
                </h2>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div
                        v-for="(stat, index) in statsPerTipe"
                        :key="index"
                        class="flex flex-col gap-2 rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800"
                    >
                        <!-- Icon Container -->
                        <div class="flex items-center justify-between">
                            <div
                                class="rounded-lg p-2 text-white"
                                :class="stat.warna"
                            >
                                <!-- Icons -->
                                <svg
                                    v-if="stat.icon === 'globe'"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    v-else-if="stat.icon === 'building'"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="stat.icon === 'user'"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-400"
                                >{{ stat.count }}x</span
                            >
                        </div>

                        <!-- Values -->
                        <div>
                            <p
                                class="text-2xl font-extrabold text-slate-900 dark:text-white"
                            >
                                {{ stat.total_jam }}
                            </p>
                            <p
                                class="text-xs font-medium text-slate-500 uppercase"
                            >
                                Jam {{ stat.tipe }}
                            </p>
                        </div>

                        <!-- Mini Bar -->
                        <div
                            class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700"
                        >
                            <div
                                class="h-full rounded-full transition-all duration-1000"
                                :class="stat.warna"
                                :style="{
                                    width:
                                        (totalJam > 0
                                            ? (stat.total_jam / totalJam) * 100
                                            : 0) + '%',
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Section: Jadwal Internal -->
                <!-- Section: Jadwal Gabungan -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-base font-bold text-slate-900 dark:text-white"
                        >
                            Jadwal Mendatang
                        </h2>
                        <a
                            href="#"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                            >Lihat Semua</a
                        >
                    </div>

                    <div
                        v-if="jadwalInternal.length > 0"
                        class="flex flex-col gap-3"
                    >
                        <div
                            v-for="item in jadwalInternal"
                            :key="item.id + '-' + item.tipe"
                            class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-800"
                        >
                            <!-- Date Box -->
                            <div
                                class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                            >
                                <span class="text-xs font-bold uppercase">{{
                                    new Date(item.tanggal).toLocaleDateString(
                                        'id-ID',
                                        { month: 'short' },
                                    )
                                }}</span>
                                <span class="text-lg leading-none font-bold">{{
                                    new Date(item.tanggal).getDate()
                                }}</span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="mb-1 flex items-center gap-2">
                                    <h3
                                        class="font-semibold text-slate-900 dark:text-white"
                                    >
                                        {{ item.nama_diklat }}
                                    </h3>
                                    <!-- Badge Tipe -->
                                    <span
                                        class="rounded px-1.5 py-0.5 text-[10px] font-bold tracking-wide uppercase"
                                        :class="{
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300':
                                                item.tipe === 'Internal',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300':
                                                item.tipe === 'Eksternal',
                                            'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300':
                                                item.tipe === 'HLC',
                                        }"
                                    >
                                        {{ item.tipe }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ item.tempat }} &bull;
                                    {{ item.jam_diklat }} JP
                                </p>
                            </div>

                            <!-- Status / Action -->
                            <div class="flex flex-col items-end gap-1">
                                <span
                                    class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                    :class="{
                                        'bg-slate-100 text-slate-600':
                                            item.status === 'Terjadwal' ||
                                            item.status === 'Disetujui',
                                        'bg-amber-100 text-amber-700':
                                            item.status === 'Menunggu',
                                        'bg-red-100 text-red-700':
                                            item.status === 'Ditolak',
                                    }"
                                >
                                    {{ item.status }}
                                </span>
                                <button
                                    class="rounded-lg bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    Detail
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 dark:border-slate-700 dark:bg-slate-800/50"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mb-2 h-8 w-8 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">
                            Tidak ada jadwal mendatang.
                        </p>
                    </div>
                </div>

                <!-- Section: Status Pengajuan Gabungan -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-base font-bold text-slate-900 dark:text-white"
                        >
                            Status Pengajuan Diklat
                        </h2>
                        <a
                            href="#"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                            >Lihat Semua</a
                        >
                    </div>

                    <div
                        v-if="pendingDiklat.length > 0"
                        class="flex flex-col gap-3"
                    >
                        <div
                            v-for="item in pendingDiklat"
                            :key="item.id + '-' + item.tipe"
                            class="flex items-center gap-4 rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm transition-all hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-900/20 dark:hover:bg-amber-900/30"
                        >
                            <!-- Icon Container (Warna Dinamis) -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400"
                            >
                                <div v-html="getIconDiklat(item.tipe)"></div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="font-semibold text-slate-900 dark:text-white"
                                    >
                                        {{ item.nama_diklat }}
                                    </h3>
                                    <!-- Badge Tipe Kecil -->
                                    <span
                                        class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase"
                                        :class="getTipeClass(item.tipe)"
                                    >
                                        {{ item.tipe }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ item.penyelenggara }} &bull; Mulai
                                    {{ formatDate(item.tanggal_mulai) }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Status Badge Utama -->
                                <span
                                    class="rounded-full bg-amber-200 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-900/50 dark:text-amber-300"
                                >
                                    Menunggu
                                </span>

                                <!-- Tombol Dokumen jika ada -->
                                <a
                                    v-if="item.dokumen"
                                    :href="`/storage/${item.dokumen}`"
                                    target="_blank"
                                    class="rounded-lg border border-slate-200 bg-white p-1.5 text-slate-500 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-blue-400"
                                    title="Lihat Dokumen"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 dark:border-slate-700 dark:bg-slate-800/50"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mb-2 h-8 w-8 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">
                            Semua pengajuan sudah diproses.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
