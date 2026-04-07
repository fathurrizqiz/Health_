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
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6 md:pt-4">
            
            <!-- Employee Profile Card -->
            <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                            {{ props.karyawan.nama_karyawan }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Detail Informasi Karyawan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">NRP</span>
                        <span class="font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.nrp }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Unit Kerja</span>
                        <span class="font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.unit_kerja }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bagian</span>
                        <span class="font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.bagian }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Posisi Jabatan</span>
                        <span class="font-medium text-slate-900 dark:text-slate-200">{{ props.karyawan.posisi_jabatan }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Klinis / Non Klinis</span>
                        <span class="inline-flex w-fit items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ props.karyawan.klinis_non_klinis }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jenis Kelamin</span>
                        <span class="font-medium text-slate-900 dark:text-slate-200">{{ genderLabel }}</span>
                    </div>
                </div>
            </div>

            <!-- Table Section Container -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Table Header & Toolbar -->
                <div class="flex flex-col gap-4 border-b border-slate-200 p-6 lg:flex-row lg:items-center lg:justify-between dark:border-slate-800">
                    
                    <!-- Target Progress -->
                    <div class="flex flex-col gap-1.5">
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            Target <span class="font-semibold text-slate-900 dark:text-white">({{ props.kategori }})</span>: 
                            {{ props.totalJam }} / {{ props.target }} Jam 
                            <span :class="props.percentage >= 100 ? 'text-emerald-600' : 'text-blue-600'">
                                ({{ props.percentage }}%)
                            </span>
                        </div>
                        <div class="h-2 w-full max-w-[280px] overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div
                                class="h-full rounded-full transition-all duration-1000 ease-out"
                                :class="props.percentage >= 100 ? 'bg-emerald-500' : 'bg-blue-600'"
                                :style="{ width: Math.min(props.percentage, 100) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Actions (Search, Filter, Add) -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Search Input -->
                        <div class="relative">
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari diklat..."
                                class="h-10 w-full rounded-lg border border-slate-300 bg-white pl-10 pr-4 text-sm transition-shadow focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 sm:w-56 dark:border-slate-700 dark:bg-slate-900"
                            />
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Filter Dropdown -->
                        <select 
                            class="h-10 cursor-pointer rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 transition-colors hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                            onchange="if(this.value) window.location.href=this.value;"
                        >
                            <option value="" disabled selected>Lihat Jadwal...</option>
                            <option value="/JadwalDiklat/Internal">Internal</option>
                            <option value="/JadwalDiklat/Eksternal">Eksternal</option>
                            <option value="/JadwalDiklat/HLC">HLC</option>
                        </select>

                        <!-- Add Button -->
                        <button
                            @click="tambah"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Selesai</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Pengajar</th>
                                <th class="hidden px-6 py-4 font-semibold uppercase tracking-wider sm:table-cell">Jam</th>
                                <th class="hidden px-6 py-4 font-semibold uppercase tracking-wider lg:table-cell">Diklat</th>
                                <th class="hidden px-6 py-4 font-semibold uppercase tracking-wider lg:table-cell">Penyelenggara</th>
                                <th class="hidden px-6 py-4 font-semibold uppercase tracking-wider lg:table-cell">Status</th>
                                <th class="px-6 py-4 text-right font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="(item, index) in daftarDiklat"
                                :key="item.id"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                            >
                                <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ formatDate(item.tanggal_mulai) ?? '-' }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ formatDate(item.tanggal_selesai) ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ item.nama_diklat ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ item.pengajar }}</td>
                                <td class="hidden px-6 py-4 text-slate-700 dark:text-slate-300 sm:table-cell">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        {{ item.jam_diklat ?? '-' }} Jam
                                    </span>
                                </td>
                                <td class="hidden px-6 py-4 text-slate-700 dark:text-slate-300 lg:table-cell">{{ item.diklat }}</td>
                                <td class="hidden px-6 py-4 text-slate-700 dark:text-slate-300 lg:table-cell">{{ item.penyelenggara }}</td>
                                <td class="hidden px-6 py-4 lg:table-cell">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <!-- View Button -->
                                        <a
                                            v-if="item.file_path"
                                            :href="route('diklat.preview', item.id)"
                                            class="rounded-lg p-2 text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                            title="Lihat Berkas"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a
                                            v-if="item.source === 'user'"
                                            :href="route('diklat.edit', item.id)"
                                            class="rounded-lg p-2 text-emerald-600 transition-colors hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                                            title="Edit Data"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M13.85 22H18a2 2 0 0 0 2-2V8a2 2 0 0 0-.586-1.414l-4-4A2 2 0 0 0 14 2H6a2 2 0 0 0-2 2v6.6" />
                                                <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                                                <path d="m3.305 19.53.923-.382" />
                                                <path d="m4.228 16.852-.924-.383" />
                                                <path d="m5.852 15.228-.383-.923" />
                                                <path d="m5.852 20.772-.383.924" />
                                                <path d="m8.148 15.228.383-.923" />
                                                <path d="m8.53 21.696-.382-.924" />
                                                <path d="m9.773 16.852.922-.383" />
                                                <path d="m9.773 19.148.922.383" />
                                                <circle cx="7" cy="18" r="3" />
                                            </svg>
                                        </a>

                                        <!-- Delete Button -->
                                        <button
                                            v-if="item.source === 'user'"
                                            @click="openModal(item.id)"
                                            class="rounded-lg p-2 text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30"
                                            title="Hapus Data"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 21H8a2 2 0 0 1-1.42-.587l-3.994-3.999a2 2 0 0 1 0-2.828l10-10a2 2 0 0 1 2.829 0l5.999 6a2 2 0 0 1 0 2.828L12.834 21" />
                                                <path d="m5.082 11.09 8.828 8.828" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="daftarDiklat.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <span>Tidak ada data diklat ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Menampilkan {{ daftarDiklat.length }} hasil
                        </div>
                        <div class="flex space-x-2">
                            <button
                                disabled
                                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                Previous
                            </button>
                            <button
                                disabled
                                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Modal Component -->
        <div>
            <ConfirmDeleteModal
                :show="showModal"
                @close="showModal = false"
                @confirm="destroy(selectedId)"
            />
        </div>
    </AppLayout>
</template>