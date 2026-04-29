<script setup lang="ts">
import HeaderMenu from '@/components/HeaderMenu.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProgramModal from '@/pages/RencanaDiklat/HLC/ProgramModal.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

// --- Breadcrumbs & Menu ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
];

const page = usePage();
const menuItems = [
    { title: 'Internal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Eksternal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
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
// const programs = ref(props.program);
const programs = computed(() => page.props.program as Program[]);
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
    if (!newProgram.value.nama_program?.trim()) {
        toast.error('Nama program tidak boleh kosong!');
        return;
    }

    isLoading.value = true;
    router.post('/HLC/Home/storeProgram', newProgram.value, {
        onSuccess: () => {
            toast.success('Program berhasil dibuat!');
            resetToPage1();
            closeModal();
            // Auto-refresh the 'program' prop/data
            router.reload({ only: ['program'] });
        },
        onError: (errors) => {
            toast.error(
                'Gagal membuat program: ' +
                    Object.values(errors).flat().join(', '),
            );
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const tambahDetail = () => {
    if (!newDetail.value.nama_diklat?.trim()) {
        toast.error('Nama diklat tidak boleh kosong!');
        return;
    }

    isLoading.value = true;
    router.post('/HLC/Home/storeDetail', newDetail.value, {
        onSuccess: () => {
            toast.success('Detail Diklat berhasil ditambahkan!');
            closeDetailModal();
            resetToPage1();
            // 👇 Auto-refresh relevant data (e.g., 'details' or 'program')
            // Adjust the key(s) based on your Inertia page props
            router.reload({ only: ['program'] }); // or ['details'], or both
        },
        onError: (errors) => {
            toast.error(
                'Gagal menambahkan detail: ' +
                    Object.values(errors).flat().join(', '),
            );
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

// edit program
const isProgramModalOpen = ref(false);
const editingProgram = ref(null); // State untuk menampung data yang akan diedit

const openAddProgram = () => {
    editingProgram.value = null; // Reset data
    isProgramModalOpen.value = true;
};

const openEditProgram = (program) => {
    editingProgram.value = program; // Isi dengan data program terpilih
    isProgramModalOpen.value = true;
};

const destroyprogram = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus program ini?')) {
        router.post(
            `/HLC/Home/destroyProgram/${id}`,
            {},
            {
                onSuccess: () => {
                    toast.success('Program berhasil dihapus!');
                    resetToPage1();
                    router.reload({ only: ['program'] });
                },
                onError: () => {
                    toast.error('Gagal menghapus program.');
                },
            },
        );
    }
};

// edit detail diklat
// --- State ---
// Hapus state modal detail & autocomplete lama, ganti dengan ini:
const isDiklatModalOpen = ref(false);
const selectedProgramIdForDiklat = ref<number | null>(null);
const editingDiklat = ref<any>(null); // State menampung data diklat yang akan diedit

// --- Functions ---

// 1. Fungsi untuk buka modal TAMBAH diklat
const openAddDiklatModal = (programId: number) => {
    editingDiklat.value = null; // Reset karena mode tambah
    selectedProgramIdForDiklat.value = programId;
    isDiklatModalOpen.value = true;
};

// 2. Fungsi untuk buka modal EDIT diklat
const openEditDiklatModal = (programId: number, diklatRowData: any) => {
    // diklatRowData adalah data dari loop table 'row'
    editingDiklat.value = diklatRowData;
    selectedProgramIdForDiklat.value = programId;
    isDiklatModalOpen.value = true;
};

const closeDiklatModal = () => {
    isDiklatModalOpen.value = false;
    editingDiklat.value = null;
};

// --- Fungsi Hapus Detail (Saran) ---
const destroyDetail = (id: number) => {
    if (confirm('Yakin ingin menghapus data peserta diklat ini?')) {
        router.post(
            `/HLC/Home/destroyDetail/${id}`,
            {},
            {
                onSuccess: () => toast.success('Data berhasil dihapus'),
            },
        );
    }
};
</script>

<template>
    <Head title="HLC" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />

        <div class="p-4 md:p-6 lg:p-8">
            <!-- Header Section -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-white"
                    >
                        Human Learning Center (HLC)
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Kelola data program pelatihan dan detail diklat peserta.
                    </p>
                </div>
                <button
                    @click="openAddProgram"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                >
                    <svg
                        class="h-5 w-5"
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

            <!-- Filters & Controls Toolbar -->
            <div
                class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-end dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex-1">
                    <label
                        class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >Cari Program</label
                    >
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            @input="resetToPage1"
                            type="text"
                            placeholder="Ketik nama program..."
                            class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 pr-4 pl-10 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                        />
                        <svg
                            class="absolute top-2.5 left-3 h-5 w-5 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            ></path>
                        </svg>
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <label
                        class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >Tahun</label
                    >
                    <select
                        v-model="selectedYear"
                        @change="resetToPage1"
                        class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
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
                <div class="w-full md:w-48">
                    <label
                        class="mb-1.5 block text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >Tampilan</label
                    >
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

            <!-- List Summary -->
            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Menampilkan
                <span class="text-slate-900 dark:text-white">{{
                    paginatedPrograms.length
                }}</span>
                dari
                <span class="text-slate-900 dark:text-white">{{
                    filteredPrograms.length
                }}</span>
                program
            </div>

            <!-- Program List -->
            <div v-if="paginatedPrograms.length > 0" class="space-y-6">
                <div
                    v-for="program in paginatedPrograms"
                    :key="program.id"
                    class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                >
                    <!-- Card Header -->
                    <div
                        class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800 dark:bg-slate-800/50"
                    >
                        <div>
                            <h3
                                class="text-xl font-bold text-slate-800 dark:text-white"
                            >
                                {{ program.nama_program }}
                            </h3>
                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-0.5 text-xs font-semibold text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    Tahun: {{ program.tahun }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="openDetailModal(program.id)"
                                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500/50 focus:outline-none"
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
                            </button>
                            <button
                                @click="openEditProgram(program)"
                                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="17"
                                    height="17"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#ffffff"
                                    stroke-width="1"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-pencil-icon lucide-pencil"
                                >
                                    <path
                                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                    />
                                    <path d="m15 5 4 4" />
                                </svg>
                            </button>
                            <button
                                @click="destroyprogram(program.id)"
                                class="text-red-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#ff0000"
                                    stroke-width="1.25"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-trash-icon lucide-trash"
                                >
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"
                                    />
                                    <path d="M3 6h18" />
                                    <path
                                        d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Card Body / Nested Table -->
                    <div class="p-5">
                        <div
                            v-if="program.hlc.length > 0"
                            class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700"
                        >
                            <table
                                class="w-full text-left text-sm whitespace-nowrap"
                            >
                                <thead
                                    class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400"
                                >
                                    <tr>
                                        <th
                                            class="px-4 py-3 font-semibold tracking-wider uppercase"
                                        >
                                            Nama Diklat
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold tracking-wider uppercase"
                                        >
                                            Pengajar
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold tracking-wider uppercase"
                                        >
                                            Penyelenggara
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold tracking-wider uppercase"
                                        >
                                            Peserta
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-slate-100 dark:divide-slate-800"
                                >
                                    <tr
                                        v-for="row in program.hlc"
                                        :key="row.id"
                                        class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                                    >
                                        <td
                                            class="px-4 py-3 font-medium text-slate-900 dark:text-slate-200"
                                        >
                                            {{ row.nama_diklat }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-slate-600 dark:text-slate-300"
                                        >
                                            {{ row.pengajar }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-slate-600 dark:text-slate-300"
                                        >
                                            {{ row.penyelenggara }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-slate-600 dark:text-slate-300"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <div
                                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
                                                >
                                                    {{
                                                        row.karyawan?.nama_karyawan?.charAt(
                                                            0,
                                                        ) || '?'
                                                    }}
                                                </div>
                                                {{
                                                    row.karyawan
                                                        ?.nama_karyawan || '-'
                                                }}
                                            </div>
                                        </td>
                                        <td
                                            class="flex gap-2 px-4 py-3 text-sm"
                                        >
                                            <button
                                                @click="
                                                    openEditDiklatModal(
                                                        program.id,
                                                        row,
                                                    )
                                                "
                                                class="text-blue-600 hover:text-blue-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="#0080ff"
                                                    stroke-width="1"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-pencil-icon lucide-pencil"
                                                >
                                                    <path
                                                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"
                                                    />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="destroyDetail(row.id)"
                                                class="text-rose-600 hover:text-rose-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="#ff0000"
                                                    stroke-width="1"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-trash2-icon lucide-trash-2"
                                                >
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"
                                                    />
                                                    <path d="M3 6h18" />
                                                    <path
                                                        d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                                    />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center py-8 text-slate-500 dark:text-slate-400"
                        >
                            <svg
                                class="mb-2 h-10 w-10 text-slate-300 dark:text-slate-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                ></path>
                            </svg>
                            <span
                                >Belum ada data diklat untuk program ini.</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State Global -->
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white py-20 text-center dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                    <svg
                        class="h-10 w-10 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                </div>
                <h3
                    class="mt-4 text-lg font-bold text-slate-900 dark:text-white"
                >
                    Tidak ada program ditemukan
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Coba ubah kata kunci atau filter tahun pencarian Anda.
                </p>
            </div>

            <!-- ============================================== -->
            <!-- Modal Tambah Program -->
            <!-- ============================================== -->
            <!-- REfactore -->
            <!-- tambah program -->
            <ProgramModal
                :show="isProgramModalOpen"
                :program="editingProgram"
                @close="isProgramModalOpen = false"
            />
            <!-- aksi edit dan hapus detail -->
            <DiklatModal
                :show="isDiklatModalOpen"
                :program-id="selectedProgramIdForDiklat || 0"
                :diklat="editingDiklat"
                :karyawans="props.karyawans"
                @close="closeDiklatModal"
            />

            <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div
                    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                    @click="closeModal"
                ></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                        @click.stop
                    >
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h3 class="text-lg font-bold text-slate-900">
                                Tambah Program Baru
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Nama Program</label
                                    >
                                    <input
                                        v-model="newProgram.nama_program"
                                        type="text"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        placeholder="Contoh: Pelatihan Public Speaking"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Tahun</label
                                    >
                                    <input
                                        v-model="newProgram.tahun"
                                        type="number"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-end gap-3 bg-slate-50 px-6 py-4"
                        >
                            <button
                                @click="closeModal"
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200"
                            >
                                Batal
                            </button>
                            <button
                                @click="tambahProgram"
                                :disabled="isLoading"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <svg
                                    v-if="isLoading"
                                    class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span>{{
                                    isLoading
                                        ? 'Menyimpan...'
                                        : 'Simpan Program'
                                }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- Modal Tambah Diklat (Autocomplete) -->
            <!-- ============================================== -->
            <div
                v-if="isDetailModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <div
                    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                    @click="closeDetailModal"
                ></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                        @click.stop
                    >
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h3 class="text-lg font-bold text-slate-900">
                                Tambah Diklat Baru
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <!-- Nama Diklat -->
                                <div class="sm:col-span-2">
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Nama Diklat</label
                                    >
                                    <input
                                        v-model="newDetail.nama_diklat"
                                        type="text"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        placeholder="Contoh: Teknik Presentasi Efektif"
                                    />
                                </div>

                                <!-- Pengajar -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Pengajar</label
                                    >
                                    <input
                                        v-model="newDetail.pengajar"
                                        type="text"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>

                                <!-- Jam Diklat -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Jam Diklat</label
                                    >
                                    <input
                                        v-model="newDetail.jam_diklat"
                                        type="number"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>

                                <!-- Penyelenggara -->
                                <div class="sm:col-span-2">
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Penyelenggara</label
                                    >
                                    <input
                                        type="text"
                                        v-model="newDetail.penyelenggara"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>

                                <!-- Tanggal Mulai & Selesai -->
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Tanggal Mulai</label
                                    >
                                    <input
                                        v-model="newDetail.tanggal_mulai"
                                        type="date"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Tanggal Selesai</label
                                    >
                                    <input
                                        v-model="newDetail.tanggal_selesai"
                                        type="date"
                                        class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                </div>

                                <!-- Pilih Karyawan (Autocomplete) -->
                                <div class="mt-2 sm:col-span-2">
                                    <label
                                        class="mb-1 block text-sm font-medium text-slate-700"
                                        >Pilih Karyawan (Peserta)</label
                                    >
                                    <div class="relative">
                                        <input
                                            v-model="karyawanSearchQuery"
                                            @focus="openDropdown"
                                            @blur="closeDropdown"
                                            type="text"
                                            class="h-10 w-full rounded-lg border border-slate-300 pr-10 pl-3 text-sm placeholder-slate-400 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="Ketik nama atau NRP karyawan..."
                                        />
                                        <button
                                            v-if="selectedKaryawan"
                                            @click="clearKaryawan"
                                            type="button"
                                            class="absolute top-2.5 right-3 text-slate-400 hover:text-rose-500"
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

                                        <!-- Dropdown list -->
                                        <ul
                                            v-if="
                                                isDropdownOpen &&
                                                filteredKaryawans.length > 0
                                            "
                                            class="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-xl outline-none"
                                        >
                                            <li
                                                v-for="karyawan in filteredKaryawans"
                                                :key="karyawan.id"
                                                @mousedown="
                                                    selectKaryawan(karyawan)
                                                "
                                                class="flex cursor-pointer items-center px-4 py-2 text-sm text-slate-700 transition-colors select-none hover:bg-blue-50 hover:text-blue-700"
                                            >
                                                <span class="font-medium">{{
                                                    karyawan.nama_karyawan
                                                }}</span>
                                                <span
                                                    class="ml-2 text-slate-400"
                                                    >({{ karyawan.nrp }})</span
                                                >
                                            </li>
                                        </ul>
                                        <div
                                            v-else-if="
                                                isDropdownOpen &&
                                                karyawanSearchQuery
                                            "
                                            class="absolute z-10 mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 shadow-xl"
                                        >
                                            Tidak ada karyawan ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-end gap-3 bg-slate-50 px-6 py-4"
                        >
                            <button
                                @click="closeDetailModal"
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200"
                            >
                                Batal
                            </button>
                            <button
                                @click="tambahDetail"
                                :disabled="isLoading"
                                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <svg
                                    v-if="isLoading"
                                    class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span>{{
                                    isLoading ? 'Menyimpan...' : 'Simpan Diklat'
                                }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
