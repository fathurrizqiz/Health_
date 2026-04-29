<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import HeaderMenu from '@/components/HeaderMenu.vue';
import { ref, computed } from 'vue';
import { toast } from 'vue3-toastify';

// Import Komponen Modal Baru
import ProgramEksternalModal from '@/pages/RencanaDiklat/RPT/PendidikanNonFormal/ProgramEksternalModal.vue';
import DetailEksternalModal from '@/pages/RencanaDiklat/RPT/PendidikanNonFormal/DetailEksternalModal.vue';

// --- Breadcrumbs & Menu ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'Diklat Eksternal', href: '#' },
];

const page = usePage();
const auth = page.props.auth;
const rawRole = auth.user?.role || [];
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];

const menuItems = [
    { title: 'Internal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Eksternal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
];

// --- Data Interfaces ---
interface Karyawan {
    id: number;
    nrp: string;
    nama_karyawan: string;
    bagian: string;
    unit_kerja: string;
    posisi_jabatan: string;
    klinis_non_klinis: string;
    jenis_kelamin: string;
}

interface DiklatEksternal {
    id: number;
    program_id: number;
    nama_karyawan: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    jam_diklat: number;
    penyelenggara: string;
    nrp: string;
    status: string;
}

interface ProgramEksternal {
    id: number;
    nama_diklat: string;
    tahun: string;
    eksternal: DiklatEksternal[];
}

// Get data from controller
const props = defineProps<{
    karyawan: Karyawan[];
    program: ProgramEksternal[];
}>();

// --- Filter & Pagination State ---
const searchQuery = ref('');
const selectedYear = ref('all');
const itemsPerPage = ref(5);
const currentPage = ref(1);

// --- Modal & Edit State ---
const isProgramModalOpen = ref(false);
const isDetailModalOpen = ref(false);
const selectedProgramId = ref<number | null>(null);

const editingProgram = ref(null);
const editingDetail = ref(null);

// --- Computed Properties ---
const filteredPrograms = computed(() => {
    return props.program.filter(p => {
        const matchesSearch = p.nama_diklat.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesYear = selectedYear.value === 'all' || p.tahun === selectedYear.value;
        return matchesSearch && matchesYear;
    });
});

const paginatedPrograms = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredPrograms.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredPrograms.value.length / itemsPerPage.value);
});

const availableYears = computed(() => {
    const years = props.program.map(p => p.tahun);
    return ['all', ...Array.from(new Set(years)).sort((a, b) => parseInt(b) - parseInt(a))];
});

// --- Functions ---
const resetToPage1 = () => {
    currentPage.value = 1;
};

const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) currentPage.value = page;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

// Modals Triggers
const openProgramModal = (program = null) => {
    editingProgram.value = program; // null = Add, object = Edit
    isProgramModalOpen.value = true;
};

const openDetailModal = (programId: number, detail = null) => {
    selectedProgramId.value = programId;
    editingDetail.value = detail; // null = Add, object = Edit
    isDetailModalOpen.value = true;
};

// Delete Handlers
const hapusProgram = (programId: number) => {
    if (confirm('Hapus program ini beserta semua datanya?')) {
        router.delete(`/RencanaDiklat/RPT/PN/program/${programId}`, {
            onSuccess: () => {
                toast.success('Program berhasil dihapus!');
                resetToPage1();
            },
            onError: (errors) => {
                toast.error('Gagal menghapus program: ' + Object.values(errors).join(', '));
            }
        });
    }
};

const hapusDetail = (detailId: number) => {
    if (confirm('Hapus data diklat ini?')) {
        router.delete(`/RencanaDiklat/RPT/PN/Detail/${detailId}`, {
            onSuccess: () => {
                toast.success('Detail diklat berhasil dihapus!');
            },
            onError: (errors) => {
                toast.error('Gagal menghapus detail: ' + Object.values(errors).join(', '));
            }
        });
    }
};
</script>

<template>
    <Head title="Pendidikan Non Formal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />

        <div class="space-y-6 p-4 md:p-6 lg:p-8">
            
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                        Diklat Eksternal
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Kelola daftar program pelatihan dan detail peserta diklat eksternal.
                    </p>
                </div>
                <button
                    @click="openProgramModal()"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Program
                </button>
            </div>

            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-end dark:border-slate-800 dark:bg-slate-900">
                <div class="flex-1">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Cari Program</label>
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            @input="resetToPage1"
                            type="text"
                            placeholder="Ketik nama program..."
                            class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 pl-10 pr-4 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                        />
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Tahun</label>
                    <select
                        v-model="selectedYear"
                        @change="resetToPage1"
                        class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                    >
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year === 'all' ? 'Semua Tahun' : year }}
                        </option>
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Tampilan</label>
                    <select
                        v-model.number="itemsPerPage"
                        @change="resetToPage1"
                        class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                    >
                        <option :value="5">5 baris</option>
                        <option :value="10">10 baris</option>
                        <option :value="20">20 baris</option>
                    </select>
                </div>
            </div>

            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Menampilkan <span class="text-slate-900 dark:text-white">{{ paginatedPrograms.length }}</span> dari 
                <span class="text-slate-900 dark:text-white">{{ filteredPrograms.length }}</span> program
            </div>

            <div v-if="paginatedPrograms.length > 0" class="space-y-6">
                <div
                    v-for="prog in paginatedPrograms"
                    :key="prog.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800 dark:bg-slate-800/50">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">
                                {{ prog.nama_diklat }}
                            </h3>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-0.5 text-xs font-semibold text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    Tahun: {{ prog.tahun }}
                                </span>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <button
                                @click="openProgramModal(prog)"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30"
                            >
                                Edit Program
                            </button>
                            <button
                                @click="hapusProgram(prog.id)"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30"
                            >
                                Hapus
                            </button>
                            <button
                                @click="openDetailModal(prog.id)"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Peserta
                            </button>
                        </div>
                    </div>

                    <div class="p-5">
                        <div v-if="prog.eksternal && prog.eksternal.length > 0" class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                            <table class="w-full whitespace-nowrap text-left text-sm">
                                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama Karyawan</th>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">NRP</th>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Tanggal Selesai</th>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Jam Diklat</th>
                                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Penyelenggara</th>
                                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr
                                        v-for="detail in prog.eksternal"
                                        :key="detail.id"
                                        class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                                    >
                                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-200">
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                    {{ detail.nama_karyawan?.charAt(0) || '?' }}
                                                </div>
                                                {{ detail.nama_karyawan }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ detail.nrp }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ detail.tanggal_mulai }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ detail.tanggal_selesai }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                {{ detail.jam_diklat }} Jam
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ detail.penyelenggara }}</td>
                                        <td class="px-4 py-3 text-center flex justify-center gap-1">
                                            <button
                                                @click="openDetailModal(prog.id, detail)"
                                                class="rounded-lg p-2 text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                                title="Edit Diklat"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button
                                                @click="hapusDetail(detail.id)"
                                                class="rounded-lg p-2 text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30"
                                                title="Hapus Diklat"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-8 text-slate-500 dark:text-slate-400">
                            <svg class="mb-2 h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span>Belum ada data peserta diklat.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white py-20 text-center dark:border-slate-700 dark:bg-slate-900">
                <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                    <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Tidak ada program ditemukan</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Coba ubah kata kunci filter atau tambah program baru.</p>
            </div>

            <div v-if="totalPages > 1" class="flex flex-col items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 sm:flex-row dark:border-slate-800 dark:bg-slate-900">
                <div class="text-sm font-medium text-slate-600 dark:text-slate-400">
                    Halaman <span class="text-slate-900 dark:text-white">{{ currentPage }}</span> dari <span class="text-slate-900 dark:text-white">{{ totalPages }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        :disabled="currentPage === 1"
                        @click="prevPage"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    >
                        Sebelumnya
                    </button>
                    <button
                        :disabled="currentPage === totalPages"
                        @click="nextPage"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>

        </div>

        <ProgramEksternalModal 
            :show="isProgramModalOpen" 
            :program="editingProgram" 
            @close="isProgramModalOpen = false" 
        />

        <DetailEksternalModal 
            :show="isDetailModalOpen" 
            :program-id="selectedProgramId ?? 0" 
            :detail="editingDetail"
            :karyawan="props.karyawan"
            @close="isDetailModalOpen = false" 
        />

    </AppLayout>
</template>