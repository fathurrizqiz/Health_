<script setup lang="ts">
import HeaderMenu from '@/components/HeaderMenu.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// --- Breadcrumbs & Menu ---
const breadcrumbs: BreadcrumbItem[] = [
    
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
];

const page = usePage();
const menuItems = [
    { title: 'Pendidikan Formal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Pendidikan Non Formal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
    { title: 'Jadwal Non Formal', href: '/RencanaDiklat/jadwal' },
];

// --- Data Interfaces ---
interface DiklatRow {
    id: number;
    nama_diklat: string;
    pengajar: string;
    penyelenggara: string;
    program_id: number;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    jam_diklat: number;

    
}

interface Program {
    id: number;
    nama_program: string;
    tahun: string;
    hlc: DiklatRowWithKaryawan[];
}

// New interface for Karyawan
interface Karyawan {
    id: number;
    nrp: string;
    nama_karyawan: string;
}
interface DiklatRowWithKaryawan extends DiklatRow {
    karyawan: Karyawan | null; // hasil join by nrp
}


// --- Props ---
const props = defineProps<{
    program: Program[];
    karyawans: Karyawan[]; 
}>();



// --- State ---
const programs = ref(props.program);
const isLoading = ref(false);
const flashMessage = computed(
    () => page.props.flash?.success || page.props.flash?.error,
);

// state autocomplete
const karyawanSearchQuery = ref('');
const isDropdownOpen = ref(false);
const selectedKaryawan = ref<Karyawan | null>(null);

// --- Filter & Pagination State ---
const searchQuery = ref('');
const selectedYear = ref('all');
const itemsPerPage = ref(5);
const currentPage = ref(1);

// --- Modal State ---
const isModalOpen = ref(false);
const isDetailModalOpen = ref(false);
const selectedProgramId = ref<number | null>(null);

const newProgram = ref({
    nama_program: '',
    tahun: new Date().getFullYear().toString(),
});

// Updated state for the new detail modal
const newDetail = ref({
    program_id: 0,
    nama_diklat: '',
    pengajar: '',
    penyelenggara: '',
    nrp: '', // Changed from karyawan_ids array to single nrp string
    tanggal_mulai: '',
    tanggal_selesai: '',
    jam_diklat: '',
});

// --- Computed Properties ---
const filteredPrograms = computed(() => {
    return programs.value.filter((program) => {
        const matchesSearch = program.nama_program
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());
        const matchesYear =
            selectedYear.value === 'all' ||
            program.tahun === selectedYear.value;
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
    const years = programs.value.map((p) => p.tahun);
    return [
        'all',
        ...Array.from(new Set(years)).sort((a, b) => parseInt(b) - parseInt(a)),
    ];
});

const filteredKaryawans = computed(() => {
    if (!karyawanSearchQuery.value) {
        // If search is empty, show all employees
        return props.karyawans;
    }
    const query = karyawanSearchQuery.value.toLowerCase();
    return props.karyawans.filter(
        (k) =>
            k.nama_karyawan.toLowerCase().includes(query) ||
            k.nrp.toLowerCase().includes(query),
    );
});

// --- Functions ---
const resetToPage1 = () => {
    currentPage.value = 1;
};

const openModal = () => {
    newProgram.value = {
        nama_program: '',
        tahun: new Date().getFullYear().toString(),
    };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const openDetailModal = (programId: number) => {
    selectedProgramId.value = programId;
    newDetail.value = {
        program_id: programId,
        nama_diklat: '',
        pengajar: '',
        penyelenggara: '',
        nrp: '', 
        tanggal_mulai: '',
        tanggal_selesai: '',
    };
    // Reset selected karyawan
    selectedKaryawan.value = null;
    karyawanSearchQuery.value = '';
    isDetailModalOpen.value = true;
};

const closeDetailModal = () => {
    isDetailModalOpen.value = false;
    selectedProgramId.value = null;
};

const openDropdown = () => {
    isDropdownOpen.value = true;
};

const closeDropdown = () => {
    // Use a timeout to allow click on an option to register before closing
    setTimeout(() => {
        isDropdownOpen.value = false;
    }, 200);
};

const selectKaryawan = (karyawan: Karyawan) => {
    selectedKaryawan.value = karyawan;
    newDetail.value.nrp = karyawan.nrp;
    karyawanSearchQuery.value = `${karyawan.nama_karyawan} (${karyawan.nrp})`; // Display selected karyawan
};

const clearKaryawan = () => {
    selectedKaryawan.value = null;
    newDetail.value.nrp = '';
    karyawanSearchQuery.value = '';
};

const tambahProgram = () => {
    if (!newProgram.value.nama_program.trim()) {
        alert('Nama program tidak boleh kosong!');
        return;
    }
    isLoading.value = true;
    router.post('/HLC/Home/storeProgram', newProgram.value, {
        onSuccess: () => {
            router.reload({ only: ['program'] });
            closeModal();
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const tambahDetail = () => {
    if (!newDetail.value.nama_diklat.trim()) {
        alert('Nama diklat tidak boleh kosong!');
        return;
    }

    isLoading.value = true;

    // Use the updated route
    router.post('/HLC/Home/storeDetail', newDetail.value, {
        onSuccess: () => {
            closeDetailModal();
            // router.reload will automatically show the flash message
            router.reload({ only: ['program'] });
        },
        onError: (errors) => {
            console.error(errors);
            alert('Gagal menambahkan detail. Cek console untuk detail.');
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <Head title="HLC" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />

        <div class="p-6">
            <!-- Flash Message Notification -->
            <div
                v-if="flashMessage"
                :class="[
                    'mb-4 rounded p-4',
                    page.props.flash.success
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700',
                ]"
            >
                {{ flashMessage }}
            </div>

            <!-- Header, Filters, Program List (mostly unchanged) -->
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <h2 class="text-2xl font-bold text-gray-800">HLC</h2>
                <button
                    @click="openModal"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white shadow-md transition hover:bg-blue-700"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Tambah Program
                </button>
            </div>

            <!-- ... (Filter Controls and Program List are the same as the previous answer) ... -->
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                        >Cari Program</label
                    ><input
                        v-model="searchQuery"
                        @input="resetToPage1"
                        type="text"
                        placeholder="Nama program..."
                        class="w-full rounded border px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                        >Tahun</label
                    ><select
                        v-model="selectedYear"
                        @change="resetToPage1"
                        class="w-full rounded border px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option
                            v-for="year in availableYears"
                            :key="year"
                            :value="year"
                        >
                            {{ year === 'all' ? 'Semua Tahun' : year }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                        >Tampilan per Halaman</label
                    ><select
                        v-model.number="itemsPerPage"
                        @change="resetToPage1"
                        class="w-full rounded border px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="20">20</option>
                    </select>
                </div>
            </div>
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan {{ paginatedPrograms.length }} dari
                {{ filteredPrograms.length }} program
            </div>
            <div v-if="paginatedPrograms.length > 0" class="space-y-8">
                <div
                    v-for="program in paginatedPrograms"
                    :key="program.id"
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                >
                    <div
                        class="flex items-center justify-between bg-gray-50 px-6 py-4"
                    >
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">
                                {{ program.nama_program }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                Tahun: {{ program.tahun }}
                            </p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <button
                                @click="openDetailModal(program.id)"
                                class="flex items-center gap-2 rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700"
                            >
                                <svg
                                    class="h-3 w-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Tambah Diklat
                            </button>
                        </div>
                        <div
                            v-if="program.hlc.length > 0"
                            class="overflow-x-auto"
                        >
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Nama Diklat
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Pengajar
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Penyelenggara
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Peserta
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="row in program.hlc"
                                        :key="row.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-4 py-3 text-sm">
                                            {{ row.nama_diklat }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ row.pengajar }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ row.penyelenggara }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ row.karyawan?.nama_karyawan }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="py-6 text-center text-gray-500">
                            Belum ada data diklat.
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-else
                class="rounded-xl border border-gray-200 bg-white py-16 text-center shadow-sm"
            >
                <h3 class="mt-4 text-lg font-medium text-gray-900">
                    Tidak ada program yang sesuai
                </h3>
                <p class="mt-1 text-gray-500">Coba ubah filter pencarian.</p>
            </div>

            <!-- Modal Tambah Program (unchanged) -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div
                    class="bg-opacity-50 fixed inset-0 backdrop-blur-md"
                    @click="closeModal"
                ></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-md rounded-lg bg-white shadow-xl"
                        @click.stop
                    >
                        <div class="px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Tambah Program Baru
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Nama Program</label
                                    >
                                    <input
                                        v-model="newProgram.nama_program"
                                        type="text"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Contoh: Pelatihan Public Speaking"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Tahun</label
                                    >
                                    <input
                                        v-model="newProgram.tahun"
                                        type="number"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-end gap-3 bg-gray-50 px-6 py-3"
                        >
                            <button
                                @click="closeModal"
                                class="rounded px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Batal
                            </button>
                            <button
                                @click="tambahProgram"
                                :disabled="isLoading"
                                class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                <span v-if="isLoading">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Diklat (UPDATED) -->
            <div
                v-if="isDetailModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <div
                    class="bg-opacity-50 fixed inset-0 backdrop-blur-md"
                    @click="closeDetailModal"
                ></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl"
                        @click.stop
                    >
                        <div class="px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Tambah Diklat Baru
                            </h3>
                            <div
                                class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <!-- Nama Diklat -->
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Nama Diklat</label
                                    >
                                    <input
                                        v-model="newDetail.nama_diklat"
                                        type="text"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Contoh: Pelatihan Public Speaking"
                                    />
                                </div>

                                <!-- Pengajar -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Pengajar</label
                                    >
                                    <input
                                        v-model="newDetail.pengajar"
                                        type="text"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Jam Diklat</label
                                    >
                                    <input
                                        v-model="newDetail.jam_diklat"
                                        type="number"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <!-- Penyelenggara -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Penyelenggara</label
                                    >

                                    <input
                                        type="text"
                                        v-model="newDetail.penyelenggara"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <!-- Tanggal Mulai & Selesai -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Tanggal Mulai</label
                                    >
                                    <input
                                        v-model="newDetail.tanggal_mulai"
                                        type="date"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Tanggal Selesai</label
                                    >
                                    <input
                                        v-model="newDetail.tanggal_selesai"
                                        type="date"
                                        class="mt-1 block w-full rounded border px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <!-- Pilih Karyawan (Autocomplete) -->
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Pilih Karyawan</label
                                    >

                                    <!-- Container for the autocomplete -->
                                    <div class="relative mt-1">
                                        <!-- Input field for searching -->
                                        <div class="relative">
                                            <input
                                                v-model="karyawanSearchQuery"
                                                @focus="openDropdown"
                                                @blur="closeDropdown"
                                                type="text"
                                                class="block w-full rounded border border-gray-300 px-3 py-2 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Ketik nama atau NRP karyawan..."
                                            />
                                            <button
                                                v-if="selectedKaryawan"
                                                @click="clearKaryawan"
                                                type="button"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Dropdown list -->
                                        <ul
                                            v-if="
                                                isDropdownOpen &&
                                                filteredKaryawans.length > 0
                                            "
                                            class="ring-opacity-5 absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black focus:outline-none sm:text-sm"
                                        >
                                            <li
                                                v-for="karyawan in filteredKaryawans"
                                                :key="karyawan.id"
                                                @mousedown="
                                                    selectKaryawan(karyawan)
                                                "
                                                class="cursor-pointer py-2 pr-9 pl-3 select-none hover:bg-gray-100"
                                            >
                                                <div class="flex">
                                                    <span class="font-normal">{{
                                                        karyawan.nama_karyawan
                                                    }}</span>
                                                    <span
                                                        class="ml-2 truncate text-gray-500"
                                                        >({{
                                                            karyawan.nrp
                                                        }})</span
                                                    >
                                                </div>
                                            </li>
                                        </ul>
                                        <div
                                            v-else-if="
                                                isDropdownOpen &&
                                                karyawanSearchQuery
                                            "
                                            class="absolute z-10 mt-1 w-full rounded-md bg-white px-3 py-1 text-sm text-gray-500 shadow-lg"
                                        >
                                            Tidak ada karyawan ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-end gap-3 bg-gray-50 px-6 py-3"
                        >
                            <button
                                @click="closeDetailModal"
                                class="rounded px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Batal
                            </button>
                            <button
                                @click="tambahDetail"
                                :disabled="isLoading"
                                class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50"
                            >
                                <span v-if="isLoading">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
