<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import HeaderMenu from '@/components/HeaderMenu.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { toast } from 'vue3-toastify';

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

// --- Data from Controller ---
interface Karyawan {
    id: number;
    nrp: string;
    nama_karyawan: string;
    bagian: string;
    unit_kerja:string;
    posisi_jabatan:string;
    klinis_non_klinis:string;
    jenis_kelamin:string;
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

// --- Modal State ---
const isProgramModalOpen = ref(false);
const isDetailModalOpen = ref(false);
const selectedProgramId = ref<number | null>(null);

const newProgram = ref({
    nama_diklat: '',
    tahun: new Date().getFullYear().toString(),
});

const newDetail = ref({
    program_id: '',
    nama_karyawan: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    jam_diklat: 0,
    penyelenggara: '',
    nrp: '',
});

// Autocomplete state
const employeeSearchQuery = ref('');
const showEmployeeResults = ref(false);
const selectedEmployeeIndex = ref(-1);

// --- Computed: Data yang difilter & dipaginasi ---
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

// Filtered employees for autocomplete
const filteredEmployees = computed(() => {
    if (!employeeSearchQuery.value) return [];
    
    const query = employeeSearchQuery.value.toLowerCase();
    return props.karyawan.filter(k => 
        k.nama_karyawan.toLowerCase().includes(query) || 
        k.nrp.toLowerCase().includes(query)
    ).slice(0, 10); // Limit to 10 results
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

// --- Fungsi CRUD ---
const openProgramModal = () => {
    newProgram.value = { nama_diklat: '', tahun: new Date().getFullYear().toString() };
    isProgramModalOpen.value = true;
};

const closeProgramModal = () => {
    isProgramModalOpen.value = false;
};

const openDetailModal = (programId: number) => {
    selectedProgramId.value = programId;
    newDetail.value = {
        program_id: programId.toString(),
        nama_karyawan: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        jam_diklat: 0,
        penyelenggara: '',
        nrp: '',
    };
    // Reset autocomplete state
    employeeSearchQuery.value = '';
    showEmployeeResults.value = false;
    selectedEmployeeIndex.value = -1;
    isDetailModalOpen.value = true;
};

const closeDetailModal = () => {
    isDetailModalOpen.value = false;
    selectedProgramId.value = null;
};

const tambahProgram = () => {
    if (!newProgram.value.nama_diklat.trim()) {
        toast.error('Nama program tidak boleh kosong!');
        return;
    }
    
    router.post('/RencanaDiklat/RPT/PN/Program', newProgram.value, {
        onSuccess: () => {
            closeProgramModal();
            toast.success('Program Berhasil dibuat!')
            resetToPage1();
        },
        onError: (errors) => {
            toast.error('Gagal membuat program: ' + Object.values(errors).join(', '));
        }
    });
};

const tambahDetail = () => {
    if (!newDetail.value.nama_karyawan.trim() || !newDetail.value.tanggal_mulai || !newDetail.value.tanggal_selesai) {
        toast.error('Mohon lengkapi data yang diperlukan!');
        return;
    }
    
    router.post('/RencanaDiklat/RPT/PN/Detail', newDetail.value, {
        onSuccess: () => {
            closeDetailModal();
            toast.success('Diklat Eksternal Berhasil dibuat!')
            resetToPage1();
        },
        onError: (errors) => {
            toast.error('Gagal menambah detail: ' + Object.values(errors).join(', '));
        }
    });
};

const hapusProgram = (programId: number) => {
    if (confirm('Hapus program ini beserta semua datanya?')) {
        router.delete(`/rencana-diklat/non-formal/program/${programId}`, {
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
        router.delete(`/rencana-diklat/non-formal/detail/${detailId}`, {
            onSuccess: () => {
                toast.success('Detail diklat berhasil dihapus!');
                resetToPage1();
            },
            onError: (errors) => {
                toast.error('Gagal menghapus detail: ' + Object.values(errors).join(', '));
            }
        });
    }
};

// Autocomplete functions
const selectEmployee = (employee: Karyawan) => {
    newDetail.value.nrp = employee.nrp;
    newDetail.value.nama_karyawan = employee.nama_karyawan;
    employeeSearchQuery.value = `${employee.nrp} - ${employee.nama_karyawan}`;
    showEmployeeResults.value = false;
    selectedEmployeeIndex.value = -1;
};

const handleEmployeeSearch = () => {
    showEmployeeResults.value = employeeSearchQuery.value.length > 0;
    selectedEmployeeIndex.value = -1;
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (!showEmployeeResults.value || filteredEmployees.value.length === 0) return;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedEmployeeIndex.value = Math.min(selectedEmployeeIndex.value + 1, filteredEmployees.value.length - 1);
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedEmployeeIndex.value = Math.max(selectedEmployeeIndex.value - 1, -1);
            break;
        case 'Enter':
            event.preventDefault();
            if (selectedEmployeeIndex.value >= 0) {
                selectEmployee(filteredEmployees.value[selectedEmployeeIndex.value]);
            }
            break;
        case 'Escape':
            showEmployeeResults.value = false;
            selectedEmployeeIndex.value = -1;
            break;
    }
};

const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.autocomplete-container')) {
        showEmployeeResults.value = false;
        selectedEmployeeIndex.value = -1;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
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
                    @click="openProgramModal"
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
                    v-for="prog in paginatedPrograms"
                    :key="prog.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                >
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ prog.nama_diklat }}</h3>
                            <p class="text-sm text-gray-500">Tahun: {{ prog.tahun }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="openDetailModal(prog.id)"
                                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Diklat
                            </button>
                            <button
                                @click="hapusProgram(prog.id)"
                                class="text-red-600 hover:text-red-800 font-medium text-sm"
                            >
                                Hapus Program
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="prog.eksternal && prog.eksternal.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Karyawan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NRP</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jam Diklat</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penyelenggara</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="detail in prog.eksternal" :key="detail.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ detail.nama_karyawan }}</td>
                                        <td class="px-4 py-3">{{ detail.nrp }}</td>
                                        <td class="px-4 py-3">{{ detail.tanggal_mulai }}</td>
                                        <td class="px-4 py-3">{{ detail.tanggal_selesai }}</td>
                                        <td class="px-4 py-3">{{ detail.jam_diklat }}</td>
                                        <td class="px-4 py-3">{{ detail.penyelenggara }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button
                                                @click="hapusDetail(detail.id)"
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
                <p class="mt-1 text-gray-500">Coba ubah filter pencarian atau tambah program baru.</p>
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
        <div v-if="isProgramModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 backdrop-blur-md bg-opacity-50" @click="closeProgramModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl" @click.stop>
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Tambah Program Baru</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Program</label>
                                <input
                                    v-model="newProgram.nama_diklat"
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
                        <button @click="closeProgramModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded">
                            Batal
                        </button>
                        <button @click="tambahProgram" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Detail -->
        <div v-if="isDetailModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 backdrop-blur-md bg-opacity-50" @click="closeDetailModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl" @click.stop>
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Tambah Diklat Baru</h3>
                        <div class="mt-4 space-y-4">
                            <div class="autocomplete-container relative">
                                <label class="block text-sm font-medium text-gray-700">Cari Karyawan (NRP/Nama)</label>
                                <input
                                    v-model="employeeSearchQuery"
                                    @input="handleEmployeeSearch"
                                    @keydown="handleKeyDown"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ketik NRP atau nama karyawan..."
                                />
                                
                                <!-- Autocomplete Results -->
                                <div v-if="showEmployeeResults && filteredEmployees.length > 0" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md overflow-auto">
                                    <ul class="py-1">
                                        <li 
                                            v-for="(employee, index) in filteredEmployees" 
                                            :key="employee.id"
                                            :class="[
                                                'px-3 py-2 cursor-pointer',
                                                index === selectedEmployeeIndex ? 'bg-blue-100' : 'hover:bg-gray-100'
                                            ]"
                                            @click="selectEmployee(employee)"
                                        >
                                            <div class="font-medium">{{ employee.nama_karyawan }}</div>
                                            <div class="text-sm text-gray-500">{{ employee.nrp }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NRP Karyawan</label>
                                <input
                                    v-model="newDetail.nrp"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    readonly
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                                <input
                                    v-model="newDetail.nama_karyawan"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    readonly
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input
                                    v-model="newDetail.tanggal_mulai"
                                    type="date"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input
                                    v-model="newDetail.tanggal_selesai"
                                    type="date"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Diklat per Hari</label>
                                <input
                                    v-model.number="newDetail.jam_diklat"
                                    type="number"
                                    min="1"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penyelenggara</label>
                                <input
                                    v-model="newDetail.penyelenggara"
                                    type="text"
                                    class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                        <button @click="closeDetailModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded">
                            Batal
                        </button>
                        <button @click="tambahDetail" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>