<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import HeaderMenu from '@/components/HeaderMenu.vue';
import { ref, computed } from 'vue';

// --- Breadcrumbs & Menu (sama seperti sebelumnya) ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'Pendidikan Non Formal', href: '/pendidikan-non-formal' },
];

const page = usePage();
const auth = page.props.auth;
const rawRole = auth.user?.role || [];
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];

const menuItems = [
    { title: 'Pendidikan Formal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Pendidikan Non Formal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
    { title: 'Jadwal Non Formal', href: '/RencanaDiklat/jadwal' },
    // { title: 'Eksternal', href: '/RencanaDiklat/RPT/EKST' },
];

// --- Data Program ---
interface DiklatRow {
    id: number;
    nama: string;
    peserta: string;
    pengajar: string;
    keterangan: string;
    biaya: number | null;
    total_biaya: number | null;
}

interface Program {
    id: number;
    nama_program: string;
    tahun: string;
    rows: DiklatRow[];
}

const programs = ref<Program[]>([
    // Contoh data awal (opsional)
    // {
    //     id: 1,
    //     nama_program: "Pelatihan Public Speaking",
    //     tahun: "2025",
    //     rows: []
    // }
]);

// --- Filter & Pagination State ---
const searchQuery = ref('');
const selectedYear = ref('all');
const itemsPerPage = ref(5);
const currentPage = ref(1);

// --- Modal State ---
const isModalOpen = ref(false);
const newProgram = ref({
    nama_program: '',
    tahun: new Date().getFullYear().toString(),
});

// --- Computed: Data yang difilter & dipaginasi ---
const filteredPrograms = computed(() => {
    return programs.value.filter(program => {
        const matchesSearch = program.nama_program.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesYear = selectedYear.value === 'all' || program.tahun === selectedYear.value;
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
    const years = programs.value.map(p => p.tahun);
    return ['all', ...Array.from(new Set(years)).sort((a, b) => parseInt(b) - parseInt(a))];
});

// --- Fungsi Navigasi ---
const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// --- Reset ke halaman 1 saat filter berubah ---
const resetToPage1 = () => {
    currentPage.value = 1;
};

// Watcher tidak digunakan, jadi kita panggil reset manual saat filter berubah
// (dilakukan via @input/@change di template)

// --- Fungsi CRUD (sama seperti sebelumnya) ---
const openModal = () => {
    newProgram.value = { nama_program: '', tahun: new Date().getFullYear().toString() };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const tambahProgram = () => {
    if (!newProgram.value.nama_program.trim()) {
        alert('Nama program tidak boleh kosong!');
        return;
    }
    programs.value.push({
        id: Date.now(),
        nama_program: newProgram.value.nama_program,
        tahun: newProgram.value.tahun,
        rows: [],
    });
    closeModal();
    resetToPage1(); // Reset ke halaman 1 setelah tambah
};

const tambahBaris = (programId: number) => {
    const program = programs.value.find(p => p.id === programId);
    if (program) {
        program.rows.push({
            id: Date.now(),
            nama: '',
            peserta: '',
            pengajar: '',
            keterangan: '',
            biaya: null,
            total_biaya: null,
           
        });
    }
};

const hapusBaris = (programId: number, rowId: number) => {
    const program = programs.value.find(p => p.id === programId);
    if (program) {
        program.rows = program.rows.filter(row => row.id !== rowId);
    }
};

const hapusProgram = (programId: number) => {
    if (confirm('Hapus program ini beserta semua datanya?')) {
        programs.value = programs.value.filter(p => p.id !== programId);
        resetToPage1();
    }
};
</script>

<template>
    <Head title="Pendidikan Non Formal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />

        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Pendidikan Non Formal</h2>
                <button
                    @click="openModal"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Program
                </button>
            </div>

            <!-- Filter Controls -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Program</label>
                    <input
                        v-model="searchQuery"
                        @input="resetToPage1"
                        type="text"
                        placeholder="Nama program..."
                        class="w-full rounded border px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Filter Tahun -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select
                        v-model="selectedYear"
                        @change="resetToPage1"
                        class="w-full rounded border px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year === 'all' ? 'Semua Tahun' : year }}
                        </option>
                    </select>
                </div>

                <!-- Items Per Page -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tampilan per Halaman</label>
                    <select
                        v-model.number="itemsPerPage"
                        @change="resetToPage1"
                        class="w-full rounded border px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="20">20</option>
                    </select>
                </div>
            </div>

            <!-- Info Jumlah Data -->
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan {{ paginatedPrograms.length }} dari {{ filteredPrograms.length }} program
            </div>

            <!-- Daftar Program -->
            <div v-if="paginatedPrograms.length > 0" class="space-y-8">
                <div
                    v-for="program in paginatedPrograms"
                    :key="program.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                >
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ program.nama_program }}</h3>
                            <p class="text-sm text-gray-500">Tahun: {{ program.tahun }}</p>
                        </div>
                        <button
                            @click="hapusProgram(program.id)"
                            class="text-red-600 hover:text-red-800 font-medium text-sm"
                        >
                            Hapus Program
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <button
                                @click="tambahBaris(program.id)"
                                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Diklat
                            </button>
                        </div>

                        <div v-if="program.rows.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Diklat</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengajar</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Total Biaya</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="row in program.rows" :key="row.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3"><input v-model="row.nama" class="w-full text-sm border rounded px-2 py-1" /></td>
                                        <td class="px-4 py-3"><input v-model="row.peserta" class="w-full text-sm border rounded px-2 py-1" /></td>
                                        <td class="px-4 py-3"><input v-model.number="row.pengajar" class="w-full text-sm border rounded px-2 py-1" /></td>
                                        <td class="px-4 py-3">
                                            <select name="keterangan" v-model.number="row.keterangan" id="keterangan">
                                                <option value="EKSTERNAL">EKSTERNAL</option>
                                                <option value="INTERNAL">INTERNAL</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3"><input v-model="row.biaya" class="w-full text-sm border rounded px-2 py-1" type="number" /></td>
                                        <td class="px-4 py-3"><input v-model.number="row.total_biaya" type="number" class="w-full text-sm border rounded px-2 py-1" /></td>
                                        <td class="px-4 py-3 text-center">
                                            <button
                                                @click="hapusBaris(program.id, row.id)"
                                                class="text-blue-600 hover:text-blue-800 text-sm"
                                            >
                                                Simpan
                                            </button>
                                            <button
                                                @click="hapusBaris(program.id, row.id)"
                                                class="text-red-600 hover:text-red-800 text-sm"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else class="text-center py-6 text-gray-500">
                            Belum ada data diklat.
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada program yang sesuai</h3>
                <p class="mt-1 text-gray-500">Coba ubah filter pencarian.</p>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Halaman {{ currentPage }} dari {{ totalPages }}
                </div>
                <div class="flex items-center gap-2">
                    <button
                        :disabled="currentPage === 1"
                        @click="prevPage"
                        class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Sebelumnya
                    </button>
                    <span class="px-3 py-1">...</span>
                    <button
                        :disabled="currentPage === totalPages"
                        @click="nextPage"
                        class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Program -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl" @click.stop>
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Tambah Program Baru</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Program</label>
                                <input
                                    v-model="newProgram.nama_program"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Contoh: Pelatihan Public Speaking"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tahun</label>
                                <input
                                    v-model="newProgram.tahun"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                        <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded">
                            Batal
                        </button>
                        <button @click="tambahProgram" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>