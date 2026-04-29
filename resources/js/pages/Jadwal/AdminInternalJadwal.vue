<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// --- Interface & Props ---
interface Peserta {
    id: number;
    bagian: string;
    nrp: string;
}

interface Jadwal {
    id: number;
    tanggal?: string;
    tanggal_mulai?: string;
    nama_kegiatan?: string;
    nama_diklat?: string;
    nama_pengajar?: string;
    penyelenggara?: string;
    lokasi?: string;
    peserta?: Peserta[];
    hlc?: any[];
    eksternal?: any[];
}

const props = defineProps<{
    jadwalInternal: Jadwal[];
    jadwalHLC: any[];
    jadwalEksternal: any[];
    filters: { search: string };
}>();

// --- State ---
const activeTab = ref('internal');
const search = ref(props.filters.search || '');

const menuItems = [
    { title: 'Internal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Eksternal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
];

// --- Functions ---
const formatDate = (date: string) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

// Debounced Search
const performSearch = debounce((value: string) => {
    router.get('/Jadwal', 
        { search: value }, 
        { preserveState: true, replace: true }
    );
}, 500);

watch(search, (newValue) => {
    performSearch(newValue);
});
</script>

<template>
    <Head title="Jadwal Diklat Saya" />

    <AppLayout>
       

        <div class="space-y-6 p-4 md:p-6 lg:p-8">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Jadwal Diklat Saya
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Daftar seluruh pelatihan mendatang yang Anda ikuti.
                </p>
            </div>

            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="relative max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Cari kegiatan atau diklat..." 
                        class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 pl-10 pr-4 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                    />
                </div>

                <div class="flex gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button 
                        v-for="tab in ['internal', 'hlc', 'eksternal']" 
                        :key="tab"
                        @click="activeTab = tab"
                        :class="[
                            'px-4 py-2 text-sm font-semibold rounded-lg transition-all capitalize',
                            activeTab === tab 
                                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' 
                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'
                        ]"
                    >
                        {{ tab }}
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <div v-if="activeTab === 'internal'" class="overflow-x-auto">
                    <table v-if="jadwalInternal.length" class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase">Nama Kegiatan</th>
                                <th class="px-6 py-4 font-semibold uppercase">Pengajar</th>
                                <th class="px-6 py-4 font-semibold uppercase">Lokasi</th>
                                <th class="px-6 py-4 font-semibold uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="item in jadwalInternal" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/25">
                                <td class="px-6 py-4 font-medium text-blue-600">{{ item.nama_kegiatan }}</td>
                                <td class="px-6 py-4">{{ item.nama_pengajar }}</td>
                                <td class="px-6 py-4">{{ item.lokasi }}</td>
                                <td class="px-6 py-4 font-mono">{{ formatDate(item.tanggal!) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <EmptyState v-else />
                </div>

                <div v-if="activeTab === 'hlc'" class="overflow-x-auto">
                    <table v-if="jadwalHLC.length" class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase">Program</th>
                                <th class="px-6 py-4 font-semibold uppercase">Tanggal Mulai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template v-for="prog in jadwalHLC" :key="prog.id">
                                <tr v-for="hlc in prog.hlc" :key="hlc.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 font-medium text-emerald-600">{{ hlc.nama_diklat }}</td>
                                    <td class="px-6 py-4 italic">{{ prog.nama_program }}</td>
                                    <td class="px-6 py-4 font-mono">{{ formatDate(hlc.tanggal_mulai) }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <EmptyState v-else />
                </div>

                <div v-if="activeTab === 'eksternal'" class="overflow-x-auto">
                    <table v-if="jadwalEksternal.length" class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase">Penyelenggara</th>
                                <th class="px-6 py-4 font-semibold uppercase">Tanggal Mulai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template v-for="prog in jadwalEksternal" :key="prog.id">
                                <tr v-for="eks in prog.eksternal" :key="eks.id" class="hover:bg-slate-50">
                                    <td class="px-6 py-4 font-medium text-purple-600">{{ eks.nama_diklat }}</td>
                                    <td class="px-6 py-4">{{ eks.penyelenggara }}</td>
                                    <td class="px-6 py-4 font-mono">{{ formatDate(eks.tanggal_mulai) }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <EmptyState v-else />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts">
const EmptyState = {
    template: `
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Tidak ada jadwal ditemukan</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Jadwal mungkin sudah lewat atau tidak sesuai kata kunci.</p>
        </div>
    `
};
</script>