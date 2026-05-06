<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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

interface Filters {
    search?: string
}

const props = defineProps<{
    totalKaryawans: number;
    totalPerKategori: TargetKategori[];
    totalJamDiklat: JamDiklat;
    targetAll: number;
    realisasiPerBagian: FollowDiklat[];
    filters: {
        search?: string
    };
}>();

const search = ref(props.filters?.search || '');

// Debounce helper
function debounce(func: (...args: any[]) => void, wait: number) {
    let timeout: NodeJS.Timeout;
    return function executedFunction(...args: any[]) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

watch(
    search,
    debounce((newSearch: string) => {
        router.get(
            route('dashboard'), // Make sure this route name matches your web.php
            { search: newSearch || undefined }, // Remove param if empty
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300)
);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Search Bar: Full width di mobile -->
        <div class="px-4 pt-4 md:m-3">
            <input 
                type="text" 
                v-model="search" 
                placeholder="Cari bagian ..." 
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200" 
            />
        </div>

        <div class="flex flex-1 flex-col gap-4 p-4 md:gap-6 md:p-6">
            
            <!-- Highlight Cards Section: Stacked di mobile -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                <!-- Card 1: Total Karyawan -->
                <div class="relative flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:flex-col md:items-start md:p-6">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 md:text-sm">
                            Total Karyawan
                        </div>
                        <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">
                            {{ totalKaryawans }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Total Jam Target -->
                <div class="relative flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:flex-col md:items-start md:p-6">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 md:text-sm">
                            Total Jam Target
                        </div>
                        <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">
                            {{ targetAll }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Jam Terlaksana -->
                <div class="relative flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:flex-col md:items-start md:p-6">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 md:text-sm">
                            Terlaksana ({{ totalJamDiklat.tahun }})
                        </div>
                        <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">
                            {{ totalJamDiklat.totalJamDiklat }}
                        </div>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white md:text-lg">
                        Detail per Bagian ({{ totalJamDiklat.tahun }})
                    </h2>
                </div>

                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-medium">Nama Bagian</th>
                                <th class="px-6 py-4 font-medium">Jml. Karyawan</th>
                                <th class="px-6 py-4 font-medium">Target Jam</th>
                                <th class="px-6 py-4 font-medium">Aktual Jam</th>
                                <th class="px-6 py-4 font-medium">Pencapaian (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="item in totalPerKategori" :key="item.kategori" class="hover:bg-slate-50 dark:hover:bg-slate-800/25">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ item.kategori }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ item.totalKaryawan }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ Number(item.totalTargetJam).toFixed(1) }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ item.aktualJam }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative h-2 w-32 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                            <div class="absolute h-full rounded-full bg-blue-500" :class="{'bg-emerald-500': item.persentase >= 100}" :style="{ width: Math.min(item.persentase, 100) + '%' }"></div>
                                        </div>
                                        <span class="font-semibold text-blue-600 dark:text-blue-400" :class="{'text-emerald-600': item.persentase >= 100}">{{ item.persentase }}%</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (Visible only on mobile) -->
                <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="item in totalPerKategori" :key="item.kategori" class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <span class="font-bold text-slate-900 dark:text-white text-base">{{ item.kategori }}</span>
                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ item.persentase }}%
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4">
                            <div>
                                <p class="text-slate-500 text-xs">Karyawan</p>
                                <p class="font-medium dark:text-slate-300">{{ item.totalKaryawan }} orang</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs">Aktual Jam</p>
                                <p class="font-medium dark:text-slate-300">{{ item.aktualJam }} jam</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-slate-500 text-xs mb-1">Target: {{ Number(item.totalTargetJam).toFixed(1) }} jam</p>
                                <div class="relative h-2.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                    <div class="absolute h-full rounded-full bg-blue-500" :class="{'bg-emerald-500': item.persentase >= 100}" :style="{ width: Math.min(item.persentase, 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!totalPerKategori || totalPerKategori.length === 0" class="p-10 text-center text-slate-500">
                    Belum ada data pencapaian.
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>