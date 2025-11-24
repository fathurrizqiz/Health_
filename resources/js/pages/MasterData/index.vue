<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'Pendidikan Formal', href: '/pendidikan-formal' },
];

// 🧩 Props dari Inertia
interface Employee {
    id: number;
    nama_karyawan: string;
    tmt: string;
    nrp: string;
    bagian: string;
    unit_kerja: string;
    posisi_jabatan: string;
    klinis_non_klinis: string;
    jenis_kelamin: string;
}

interface Totals {
    totalKaryawan: number;
    byCategory: Record<string, number>;
}

interface TargetJam {
    [key: string]: number; // Dinamis: bisa ada kategori tambahan di DB
}

const props = defineProps<{
    data: Employee[];
    totals: Totals;
    targetJam: TargetJam;
    kategoriList: string[];
}>();

// 📊 State data karyawan
const employees = ref(props.data || []);

// 🔍 State untuk filter
const searchQuery = ref('');
const selectedCategory = ref('Semua');

// 🔽 Opsi kategori filter (tambahkan 'Semua' di awal)
const categoryOptions = computed(() => ['Semua', ...props.kategoriList]);

// 🧮 Filter data karyawan berdasarkan kategori dan pencarian
const filteredEmployees = computed(() => {
    let result = employees.value;

    // Filter pencarian
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (emp) =>
                emp.nama_karyawan.toLowerCase().includes(query) ||
                emp.nrp.toLowerCase().includes(query) ||
                emp.bagian.toLowerCase().includes(query) ||
                emp.unit_kerja.toLowerCase().includes(query) ||
                emp.posisi_jabatan.toLowerCase().includes(query) ||
                emp.klinis_non_klinis.toLowerCase().includes(query),
        );
    }

    // Filter kategori
    if (selectedCategory.value !== 'Semua') {
        result = result.filter(
            (emp) => emp.klinis_non_klinis === selectedCategory.value,
        );
    }

    return result;
});

// 🕒 Format tanggal (TMT)
const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
};

// State untuk modal edit
const editingCategory = ref<string | null>(null);
const newTargetJam = ref<number>(0);

// Fungsi membuka modal edit
const openEditModal = (kategori: string) => {
    editingCategory.value = kategori;
    newTargetJam.value = props.targetJam[kategori] ?? 0;
};

// Fungsi menyimpan perubahan
const saveTargetJam = () => {
    if (!editingCategory.value) return;

    // Kirim ke Laravel via Inertia POST
    router.put(
        '/MasterData/update',
        {
            kategori: editingCategory.value,
            target_jam: newTargetJam.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Jam Berhasil Diperbaharui!')
                const updated = { ...props.targetJam };
                updated[editingCategory.value!] = newTargetJam.value;
                // Karena props tidak bisa diubah langsung, kita perlu cara lain → lihat catatan di bawah
                closeEditModal();
            },
        },
    );
};

// Tutup modal
const closeEditModal = () => {
    editingCategory.value = null;
    newTargetJam.value = 0;
};
</script>

<template>
    <Head title="Master Data" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-5">
            <h1 class="text-xl font-bold">Master Data</h1>
            <h4 class="font-stretch-50%">Target Jam diklat per kategori</h4>
        </div>
        <div>
            <div class="flex justify-center gap-7">
                <div class="h-32 w-60 rounded-md bg-white shadow-2xl">
                    <div class="m-5 font-medium">
                        Klinis
                        <div class="text-xl font-bold">
                            {{ targetJam['KLINIS'] }} Jam
                        </div>
                        <div class="flex justify-end">
                            <button
                                @click="openEditModal('KLINIS')"
                                class="rounded-md bg-blue-500 px-3 py-2 text-white hover:bg-blue-700"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-32 w-60 rounded-md bg-white shadow-2xl">
                    <div class="m-5 font-medium">
                        Non Klinis
                        <div class="text-xl font-bold">
                            {{ targetJam['NON KLINIS'] }} Jam
                        </div>
                        <div class="flex justify-end">
                            <button @click="openEditModal('NON KLINIS')"
                                class="rounded-md bg-blue-500 px-3 py-2 text-white hover:bg-blue-700"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-32 w-60 rounded-md bg-white shadow-2xl">
                    <div class="m-5 font-medium">
                        Manajerial Klinis
                        <div class="text-xl font-bold">
                            {{ targetJam['MANAJERIAL KLINIS'] }} Jam
                        </div>
                        <div class="flex justify-end">
                            <button @click="openEditModal('MANAJERIAL KLINIS')"
                                class="rounded-md bg-blue-500 px-3 py-2 text-white hover:bg-blue-700"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-32 w-60 rounded-md bg-white shadow-2xl">
                    <div class="m-5 font-medium">
                        Manajerial Non Klinis
                        <div class="text-xl font-bold">
                            {{ targetJam['MANAJERIAL NON KLINIS'] }} Jam
                        </div>
                        <div class="flex justify-end">
                            <button @click="openEditModal('MANAJERIAL NON KLINIS')"
                                class="rounded-md bg-blue-500 px-3 py-2 text-white hover:bg-blue-700"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center gap-3 py-2">
                <div class="h-32 w-52 rounded-md bg-white shadow-2xl">
                    <div class="m-5 font-medium">
                        Total Karyawan
                        <div class="text-xl font-bold">
                            {{ totals.totalKaryawan }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-5">
                    <div
                        v-for="(total, kategori) in totals.byCategory"
                        :key="kategori"
                        class="h-32 w-52 rounded-md bg-white shadow-2xl"
                    >
                        <div class="m-5 font-medium">
                            {{ kategori }}
                            <div class="mt-2 text-xl font-bold">
                                {{ total }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Karyawan -->
        <div class="m-5 rounded-md bg-white p-5 shadow-2xl">
            <h2 class="mb-4 text-lg font-bold">Data Karyawan</h2>

            <!-- Filter Section -->
            <div class="mb-4 flex flex-col gap-4 md:flex-row">
                <div class="flex-1">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari berdasarkan nama, NRP, bagian, unit kerja, atau posisi jabatan..."
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                </div>
                <div class="w-full md:w-48">
                    <select
                        v-model="selectedCategory"
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option
                            v-for="option in categoryOptions"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Nama
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                NRP
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Bagian
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Unit Kerja
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Posisi Jabatan
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Kategori
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                TMT
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Jenis Kelamin
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr
                            v-for="employee in filteredEmployees"
                            :key="employee.id"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900"
                            >
                                {{ employee.nama_karyawan }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ employee.nrp }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ employee.bagian }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ employee.unit_kerja }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ employee.posisi_jabatan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="{
                                        'inline-flex rounded-full px-2 text-xs leading-5 font-semibold': true,
                                        'bg-green-100 text-green-800':
                                            employee.klinis_non_klinis ===
                                            'Klinis',
                                        'bg-blue-100 text-blue-800':
                                            employee.klinis_non_klinis ===
                                            'Non Klinis',
                                        'bg-purple-100 text-purple-800':
                                            employee.klinis_non_klinis ===
                                            'Manajerial Klinis',
                                        'bg-yellow-100 text-yellow-800':
                                            employee.klinis_non_klinis ===
                                            'Manajerial Non Klinis',
                                    }"
                                >
                                    {{ employee.klinis_non_klinis }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ formatDate(employee.tmt) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                            >
                                {{ employee.jenis_kelamin }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div
                    v-if="filteredEmployees.length === 0"
                    class="py-4 text-center"
                >
                    <p class="text-gray-500">
                        Tidak ada data yang sesuai dengan filter
                    </p>
                </div>
            </div>
        </div>
        <!-- Modal Edit Target Jam -->
        <teleport to="body">
            <div
                v-if="editingCategory"
                class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md"
            >
                <div class="w-96 rounded-lg bg-white p-6">
                    <h3 class="mb-4 text-lg font-bold">
                        Edit Target Jam - {{ editingCategory }}
                    </h3>
                    <input
                        v-model.number="newTargetJam"
                        type="number"
                        min="0"
                        class="mb-4 w-full rounded border px-3 py-2"
                        placeholder="Masukkan target jam"
                    />
                    <div class="flex justify-end gap-2">
                        <button
                            @click="closeEditModal"
                            class="rounded px-4 py-2 text-gray-600 hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            @click="saveTargetJam"
                            class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
