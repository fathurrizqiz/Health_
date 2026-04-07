<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface JamDiklat {
    tahun: number;
    totalJamDiklat: number;
}

interface TargetKategori {
    kategori: string;
    totalKaryawan: number;
    targetPerOrang: number;
    totalTargetJam: number;
    totalJamDiklat: number;
    aktualJam: number; // Tambahkan ini
    persentase: number;
}

interface FollowDiklat {
    bagian: string;
    total_karyawan_ikut: number;
    total_jam_diklat: number;
}
const props = defineProps<{
    totalKaryawans: number;
    totalPerKategori: TargetKategori[];
    totalJamDiklat: JamDiklat;
    targetAll: number;
    realisasiPerBagian: FollowDiklat[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6 md:pt-4">
            
            <!-- Highlight Cards Section -->
            <div class="grid gap-6 md:grid-cols-3">
                <!-- Card 1: Total Karyawan -->
                <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Total Karyawan
                        </div>
                        <!-- Optional: Icon slot here if you have one -->
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ totalKaryawans }}
                    </div>
                </div>

                <!-- Card 2: Total Jam Target -->
                <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Total Jam Target
                        </div>
                        <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ targetAll }}
                    </div>
                </div>

                <!-- Card 3: Jam Terlaksana -->
                <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Jam Terlaksana ({{ totalJamDiklat.tahun }})
                        </div>
                        <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ totalJamDiklat.totalJamDiklat }}
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <!-- Table Header -->
                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h2 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                        Detail Pencapaian per Bagian ({{ totalJamDiklat.tahun }})
                    </h2>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                                <th class="px-6 py-4 font-medium">Nama Bagian</th>
                                <th class="px-6 py-4 font-medium">Jml. Karyawan</th>
                                <th class="px-6 py-4 font-medium">Target Jam</th>
                                <th class="px-6 py-4 font-medium">Aktual Jam</th>
                                <th class="px-6 py-4 font-medium">Pencapaian (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="item in totalPerKategori"
                                :key="item.kategori"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                            >
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">
                                    {{ item.kategori }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    {{ item.totalKaryawan }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    {{ Number(item.totalTargetJam).toFixed(1) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">
                                    {{ item.aktualJam }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Progress Bar -->
                                        <div class="relative h-2 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800 md:w-32">
                                            <div
                                                class="absolute left-0 top-0 h-full rounded-full transition-all duration-1000 ease-out"
                                                :class="item.persentase >= 100 ? 'bg-emerald-500' : 'bg-blue-500'"
                                                :style="{ width: Math.min(item.persentase, 100) + '%' }"
                                            ></div>
                                        </div>
                                        <!-- Text Percentage -->
                                        <span
                                            class="font-semibold"
                                            :class="item.persentase >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400'"
                                        >
                                            {{ item.persentase }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State Fallback -->
                            <tr v-if="!totalPerKategori || totalPerKategori.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data pencapaian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>