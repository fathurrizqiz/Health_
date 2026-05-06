<script setup lang="ts">
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import Input from '@/components/ui/input/Input.vue';
import { formatDate } from '@/helpers/date';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { toast } from 'vue3-toastify';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Diklat Karyawan',
        href: '#',
    },
];

interface Karyawan {
    nama_karyawan: string;
    nrp: string;
    bagian: string;
    unit_kerja: string;
    posisi_jabatan: string;
    klinis_non_klinis: string;
    jenis_kelamin: string;
}

interface Diklat {
    id: number;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    nama_diklat: string | null;
    pengajar: string;
    penyelenggara: string | null;
    jam_diklat: number;
    diklat: string;
    status: string;
    file_path: string | null;
    created_at: string;
    updated_at: string;
    source: 'user' | 'admin';
}
interface Admin {
    id: number;
    nama_diklat: string;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    pengajar: string;
    penyelenggara: string;
    diklat: string;
    jam_diklat: number;
    status: string;
    file_path: string | null;
    source: 'user' | 'admin';
}

interface DiklatEksternal {
    id: number;
    nama_diklat?: string;
    program_id: number;
    nama_karyawan: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    jam_diklat: number;
    penyelenggara: string;
    nrp: string;
    status: string;
}

const props = defineProps<{
    diklat: Diklat[];
    totalJam: number;
    target: number;
    percentage: number;
    kategori: string;
    karyawan: Karyawan;
    admin: Admin[];
    eksternal: DiklatEksternal[];
    search: string;
}>();

const genderLabel = computed(() => {
    const g = props.karyawan.jenis_kelamin;
    if (g == 'L') return 'LAKI-LAKI';
    if (g == 'P') return 'PEREMPUAN';
    return '-';
});

const daftarDiklat = computed(() => {
    // Gabungkan kedua array dan tambahkan penanda sumber jika perlu
    const userDiklat = (props.diklat || []).map((item) => ({
        ...item,
        source: 'user',
    }));
    const adminDiklat = (props.admin || []).map((item) => ({
        ...item,
        source: 'admin',
        file_path: null,
    }));
    const diklatEksternal = (props.eksternal || []).map((item) => ({
        ...item,
        source: 'admin',
        file_path: null,
    }));
    return [...userDiklat, ...adminDiklat, ...diklatEksternal];
});

// State management
const searchQuery = ref(props.search || '');

// Lifecycle
onMounted(() => {});


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
    searchQuery,
    debounce((newSearch: string) => {
        router.get(
            route('diklat.home'), // Make sure this route name matches your web.php
            { search: newSearch || undefined }, // Remove param if empty
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300)
);

function tambah() {
    router.visit(`/Diklat/create`);
}

const showModal = ref<boolean>(false);
const selectedId = ref<number | null>(null);

function openModal(id: number) {
    selectedId.value = id;
    showModal.value = true;
}

function destroy(id: number | null) {
    if (id === null) return; // because TypeScript will complain (and it’s right)

    router.delete(`/Diklat/destroy/${id}`, {
        onSuccess: () => {
            toast.success('Data Berhasil Dihapus');
            showModal.value = false;
            location.reload();
        },
    });
}

// const page = usePage();
// const auth = page.props.auth;
// const rawRole = auth.user?.role || [];
// const roles = Array.isArray(rawRole) ? rawRole : [rawRole];
</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 md:gap-6 md:p-6">
            
            <!-- Employee Profile Card -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 p-5 dark:border-slate-800 md:px-6">
                    <h2 class="text-xl font-bold tracking-tight text-slate-800 dark:text-white md:text-2xl">
                        {{ props.karyawan.nama_karyawan }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Detail Informasi Karyawan</p>
                </div>

                <!-- Profile Grid: 2 columns on mobile, 4 on lg -->
                <div class="grid grid-cols-2 gap-4 p-5 md:p-6 lg:grid-cols-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NRP</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.nrp }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.unit_kerja }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bagian</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.bagian }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Klinis/Non</span>
                        <span class="inline-flex w-fit items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ props.karyawan.klinis_non_klinis }}
                        </span>
                    </div>
                    <div class="col-span-2 flex flex-col gap-1 lg:col-span-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jabatan</span>
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.posisi_jabatan }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Section -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Toolbar: Stacked on mobile -->
                <div class="flex flex-col gap-5 p-5 md:p-6 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 dark:border-slate-800">
                    <!-- Progress Info -->
                    <div class="flex flex-col gap-2">
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            Target <span class="font-bold text-slate-900 dark:text-white">{{ props.kategori }}</span>: 
                            <div class="mt-1">
                                <span class="text-lg font-bold">{{ props.totalJam }}</span> / {{ props.target }} Jam 
                                <span :class="props.percentage >= 100 ? 'text-emerald-600' : 'text-blue-600'">({{ props.percentage }}%)</span>
                            </div>
                        </div>
                        <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-1000" :class="props.percentage >= 100 ? 'bg-emerald-500' : 'bg-blue-600'" :style="{ width: Math.min(props.percentage, 100) + '%' }"></div>
                        </div>
                    </div>

                    <!-- Actions Bar -->
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1 sm:w-64">
                            <Input v-model="searchQuery" placeholder="Cari diklat..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border-slate-200 dark:bg-slate-800 dark:border-slate-700" />
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        
                        <div class="flex gap-2">
                            <select class="flex-1 rounded-xl border-slate-200 bg-white px-3 py-2 text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200" onchange="if(this.value) window.location.href=this.value;">
                                <option value="" disabled selected>Jadwal...</option>
                                <option value="/JadwalDiklat/Internal">Internal</option>
                                <option value="/JadwalDiklat/Eksternal">Eksternal</option>
                                <option value="/JadwalDiklat/HLC">HLC</option>
                            </select>

                            <button @click="tambah" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="hidden sm:inline">Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 font-bold uppercase text-[10px]">No</th>
                                <th class="px-6 py-4 font-bold uppercase text-[10px]">Tgl Pelaksanaan</th>
                                <th class="px-6 py-4 font-bold uppercase text-[10px]">Nama Diklat</th>
                                <th class="px-6 py-4 font-bold uppercase text-[10px]">Jam</th>
                                <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                                <th class="px-6 py-4 text-right font-bold uppercase text-[10px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="(item, index) in daftarDiklat" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/25">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium dark:text-slate-200">
                                    {{ formatDate(item.tanggal_mulai) }} s/d {{ formatDate(item.tanggal_selesai) }}
                                </td>
                                <td class="px-6 py-4 dark:text-slate-300">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ item.nama_diklat }}</div>
                                    <div class="text-xs text-slate-500">{{ item.pengajar }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-bold">{{ item.jam_diklat }} Jm</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs px-2 py-1 bg-slate-50 dark:bg-slate-800 rounded-full">{{ item.status }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <a v-if="item.file_path" :href="route('diklat.preview', item.id)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="1.5"/></svg></a>
                                        <a v-if="item.source === 'user'" :href="route('diklat.edit', item.id)" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="1.5"/></svg></a>
                                        <button v-if="item.source === 'user'" @click="openModal(item.id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="1.5"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List (Visible on Mobile) -->
                <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="(item, index) in daftarDiklat" :key="item.id" class="p-5 relative">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-slate-400">#{{ index + 1 }}</span>
                            <div class="flex gap-1">
                                <a v-if="item.file_path" :href="route('diklat.preview', item.id)" class="p-1.5 text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg></a>
                                <a v-if="item.source === 'user'" :href="route('diklat.edit', item.id)" class="p-1.5 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg></a>
                                <button v-if="item.source === 'user'" @click="openModal(item.id)" class="p-1.5 text-rose-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg></button>
                            </div>
                        </div>
                        
                        <h4 class="font-bold text-slate-900 dark:text-white leading-tight mb-1">{{ item.nama_diklat }}</h4>
                        <p class="text-xs text-slate-500 mb-3">{{ item.pengajar }}</p>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Tanggal</p>
                                <p class="text-xs dark:text-slate-300">{{ formatDate(item.tanggal_mulai) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Durasi</p>
                                <p class="text-xs font-bold dark:text-slate-300">{{ item.jam_diklat }} Jam</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty & Footer -->
                <div v-if="daftarDiklat.length === 0" class="p-10 text-center text-slate-500">Tidak ada data.</div>

                <div class="border-t border-slate-100 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-slate-900/50 rounded-b-2xl">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-xs font-medium text-slate-500 text-center sm:text-left">Menampilkan {{ daftarDiklat.length }} hasil</span>
                        <div class="flex justify-center gap-2">
                            <button disabled class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700 opacity-50">Prev</button>
                            <button disabled class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-700 opacity-50">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteModal :show="showModal" @close="showModal = false" @confirm="destroy(selectedId)" />
    </AppLayout>
</template>