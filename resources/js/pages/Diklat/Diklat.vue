<script setup lang="ts">
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import { formatDate } from '@/helpers/date';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Detail Diklat',
        href: '/admins',
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
    nama_diklat?:string;
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
const searchQuery = ref('');

// Lifecycle
onMounted(() => {});

function jadwal() {
    router.visit(`/RencanaDiklat/jadwal`);
}

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
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Statistics Cards -->
            <div class="m-5">
                <p class="py-5 text-xl font-bold text-gray-500">
                    {{ props.karyawan.nama_karyawan }}
                </p>
                <div class="h-56 w-full rounded bg-white shadow">
                    <div class="flex justify-between p-3 py-3">
                        <div>
                            <div class="py-2">
                                NRP : {{ props.karyawan.nrp }}
                            </div>
                            <div class="py-2">
                                Unit Kerja : {{ props.karyawan.unit_kerja }}
                            </div>
                            <div class="py-2">
                                KLINIS / NON KLINIS :
                                {{ props.karyawan.klinis_non_klinis }}
                            </div>
                        </div>
                        <div>
                            <div class="py-2">
                                BAGIAN : {{ props.karyawan.bagian }}
                            </div>
                            <div class="py-2">
                                POSISI JABATAN :
                                {{ props.karyawan.posisi_jabatan }}
                            </div>
                            <div class="py-2">
                                JENIS KELAMIN : {{ genderLabel }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="rounded-xl bg-white shadow-sm">
                <!-- Table Header -->
                <div class="border-b border-gray-200 p-6">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h4>
                                Target ({{ props.kategori }}):
                                {{ props.totalJam }} / {{ props.target }} Jam
                                ({{ props.percentage }}%)
                            </h4>

                            <div class="mt-2 h-3 w-52 rounded-full bg-gray-200">
                                <div
                                    class="h-3 rounded-full bg-blue-600"
                                    :style="{ width: props.percentage + '%' }"
                                ></div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <!-- Search -->
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search..."
                                    class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-transparent focus:ring-2 focus:ring-blue-500 sm:w-64"
                                />
                                <svg
                                    class="absolute top-2.5 left-3 h-5 w-5 text-gray-400"
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
                            <!-- Status Filter -->

                            <!-- Add Button -->
                            <button
                                @click="jadwal"
                                class="flex items-center justify-center rounded-lg bg-green-600 px-2 py-2 text-white transition-colors hover:bg-green-700"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-eye-icon lucide-eye"
                                >
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"
                                    />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Lihat Jadwal
                            </button>
                            <button
                                @click="tambah"
                                class="flex items-center justify-center rounded-lg bg-blue-600 px-2 py-2 text-white transition-colors hover:bg-blue-700"
                            >
                                <svg
                                    class="mr-2 h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    ></path>
                                </svg>
                                Tambah Diklat
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    No
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Tanggal mulai
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Tanggal Selesai
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Nama Diklat
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Pengajar
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase sm:table-cell"
                                >
                                    Jam
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase lg:table-cell"
                                >
                                    Diklat
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase lg:table-cell"
                                >
                                    Penyelenggara
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase lg:table-cell"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="(item, index) in daftarDiklat"
                                :key="item.id"
                            >
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ formatDate(item.tanggal_mulai) ?? '-' }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{
                                        formatDate(item.tanggal_selesai) ?? '-'
                                    }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.nama_diklat ?? '-' }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.pengajar }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.jam_diklat ?? '-' }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.diklat }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.penyelenggara }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    {{ item.status }}
                                </td>
                                <td
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    <div class="flex gap-2">
                                        <a
                                            v-if="item.file_path"
                                            :href="
                                                route('diklat.preview', item.id)
                                            "
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
                                                class="lucide lucide-eye-icon lucide-eye"
                                            >
                                                <path
                                                    d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"
                                                />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </a>
                                        <a
                                            v-if="item.source === 'user'"
                                            :href="
                                                route('diklat.edit', item.id)
                                            "
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
                                                class="lucide lucide-file-cog-icon lucide-file-cog"
                                            >
                                                <path
                                                    d="M13.85 22H18a2 2 0 0 0 2-2V8a2 2 0 0 0-.586-1.414l-4-4A2 2 0 0 0 14 2H6a2 2 0 0 0-2 2v6.6"
                                                />
                                                <path
                                                    d="M14 2v5a1 1 0 0 0 1 1h5"
                                                />
                                                <path
                                                    d="m3.305 19.53.923-.382"
                                                />
                                                <path
                                                    d="m4.228 16.852-.924-.383"
                                                />
                                                <path
                                                    d="m5.852 15.228-.383-.923"
                                                />
                                                <path
                                                    d="m5.852 20.772-.383.924"
                                                />
                                                <path
                                                    d="m8.148 15.228.383-.923"
                                                />
                                                <path
                                                    d="m8.53 21.696-.382-.924"
                                                />
                                                <path
                                                    d="m9.773 16.852.922-.383"
                                                />
                                                <path
                                                    d="m9.773 19.148.922.383"
                                                />
                                                <circle cx="7" cy="18" r="3" />
                                            </svg>
                                        </a>
                                        <button
                                            v-if="item.source === 'user'"
                                            @click="openModal(item.id)"
                                            class="cursor-pointer"
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
                                                class="lucide lucide-eraser-icon lucide-eraser"
                                            >
                                                <path
                                                    d="M21 21H8a2 2 0 0 1-1.42-.587l-3.994-3.999a2 2 0 0 1 0-2.828l10-10a2 2 0 0 1 2.829 0l5.999 6a2 2 0 0 1 0 2.828L12.834 21"
                                                />
                                                <path
                                                    d="m5.082 11.09 8.828 8.828"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan 0 hasil
                        </div>
                        <div class="flex space-x-2">
                            <button
                                disabled
                                class="rounded-md border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Previous
                            </button>
                            <button
                                disabled
                                class="rounded-md border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <ConfirmDeleteModal
                :show="showModal"
                @close="showModal = false"
                @confirm="destroy(selectedId)"
            />
        </div>
    </AppLayout>
</template>
