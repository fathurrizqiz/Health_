<script setup lang="ts">
import HeaderMenu from '@/components/HeaderMenu.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue3-toastify';

const menuItems = [
    { title: 'Datar Bagian', href: '/MateriDiklat/approve' },
    { title: 'Presensi', href: '/MateriDiklat/reject' },
    { title: 'Sertifikat', href: '/MateriDiklat/reject' },
    { title: 'Dokumentasi', href: '/MateriDiklat/reject' },
];

interface Karyawan {
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
interface Periode {
    id: number;
    detail_program_id: number;
    nama_karyawan: string;
    tmt: string;
    nrp: string;
    bagian: string;
    unit_kerja: string;
    posisi_jabatan: string;
    klinis_non_klinis: string;
    jenis_kelamin: string;
}

interface Detail {
    id: number;
    program_id: number;
    nama_diklat: string;
    keterangan: string;
    pengajar: string;
}

interface PeriodeUtama {
    id: number;
    detail_program_id: number;
    tanggal: string;
    nama_pengajar: string;
    tempat: string;
}

const props = defineProps<{
    karyawan: Karyawan[];
    detail: Detail;
    periode: Periode[];
    utama:PeriodeUtama;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pendidikan Formal', href: '/pendidikan-formal' },
    { title: 'Detail', href: '#' },
    { title: 'Detail Periode', href: '#' },
];

// Unique list bagian
const allBagian = [...new Set(props.karyawan.map((k) => k.bagian))];

const search = ref('');
const selected = ref<string[]>([]);

const filtered = computed(() =>
    allBagian.filter((b) =>
        b.toLowerCase().includes(search.value.toLowerCase()),
    ),
);

function toggleBagian(bagian: string) {
    selected.value = selected.value.includes(bagian)
        ? selected.value.filter((x) => x !== bagian)
        : [...selected.value, bagian];
}

const karyawanFiltered = computed(() => {
    if (selected.value.length === 0) return rows.value;
    return rows.value.filter((k) => selected.value.includes(k.bagian));
});

const form = ref({
    bagian: [] as string[],
    detail_program_id: props.detail.id,
    periode_id: props.utama.id,
});

function store() {
    form.value.bagian = selected.value;

    router.post('/DiklatInternal/detailperiod/list/store', form.value);
}

const rows = ref<Periode[]>([...props.periode]);
const selectDelete = ref<number[]>([]);
function hapusTerpilih() {
    if (selectDelete.value.length === 0) return;

    if (!confirm('Yakin hapus data terpilih?')) return;

    router.delete(
        '/DiklatInternal/detailperiod/list/delete',
        {
            data: {
                ids: selectDelete.value
            },
            onSuccess: () => {
                rows.value = rows.value.filter(
                    k => !selectDelete.value.includes(k.id)
                );

                selectDelete.value = [];

                toast.success('Periode terpilih berhasil dihapus!');
            }
        }
    );
}

</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />
        <div class="m-5">
            <h1 class="text-xl font-bold">Detail Periode</h1>

            <!-- Filter Bagian -->
            <div class="m-10">
                <h3 class="mb-2">Bagian</h3>

                <div class="m-2 flex gap-5">
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Cari bagian"
                        class="w-80"
                    />

                    <button
                        class="rounded bg-green-500 px-2 py-2 text-white"
                        @click="store"
                    >
                        Simpan
                    </button>
                    <button
                        class="rounded bg-blue-500 px-2 py-2 text-white"
                        @click="selected = [...allBagian]"
                    >
                        Semua Bagian
                    </button>

                    <button
                        class="rounded bg-red-500 px-2 py-2 text-white"
                        @click="selected = []"
                    >
                        Hapus Semua Bagian
                    </button>
                </div>

                <!-- Tag Selected -->
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="tag in selected"
                        :key="tag"
                        class="cursor-pointer rounded bg-blue-200 px-2 py-1 text-sm"
                        @click="toggleBagian(tag)"
                    >
                        {{ tag }} ✕
                    </span>
                </div>

                <!-- Dropdown Result -->
                <div
                    v-if="search.length > 0"
                    class="mt-2 max-h-48 overflow-auto rounded border bg-white shadow"
                >
                    <div
                        v-for="b in filtered"
                        :key="b"
                        class="flex cursor-pointer items-center gap-2 p-2 hover:bg-gray-100"
                        @click="toggleBagian(b)"
                    >
                        <input
                            type="checkbox"
                            :checked="selected.includes(b)"
                        />
                        {{ b }}
                    </div>

                    <div v-if="filtered.length === 0" class="p-2 text-gray-500">
                        Tidak ditemukan
                    </div>
                </div>
            </div>

            <!-- TABLE KARYAWAN -->
            <div class="mt-10">
                <h2 class="mb-3 text-lg font-semibold">Daftar Karyawan</h2>
                <button
                    class="m-2 rounded bg-red-600 px-2 py-2 text-white hover:bg-red-800"
                    :disabled="selectDelete.length === 0"
                    @click="hapusTerpilih"
                >
                    Hapus Terpilih
                </button>

                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2"></th>
                            <th class="border px-3 py-2">No</th>
                            <th class="border px-3 py-2">Nama</th>
                            <th class="border px-3 py-2">TMT</th>
                            <th class="border px-3 py-2">NRP</th>
                            <th class="border px-3 py-2">Bagian</th>
                            <th class="border px-3 py-2">Unit Kerja</th>
                            <th class="border px-3 py-2">Posisi Jabatan</th>
                            <th class="border px-3 py-2">
                                Klinis / Non-Klinis
                            </th>
                            <th class="border px-3 py-2">Jenis Kelamin</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(k, i) in karyawanFiltered"
                            :key="k.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border px-3 py-2">
                                <input
                                    type="checkbox"
                                    :value="k.id"
                                    v-model="selectDelete"
                                />
                            </td>
                            <td class="border px-3 py-2">{{ i + 1 }}</td>
                            <td class="border px-3 py-2">
                                {{ k.nama_karyawan }}
                            </td>
                            <td class="border px-3 py-2">{{ k.tmt }}</td>
                            <td class="border px-3 py-2">{{ k.nrp }}</td>
                            <td class="border px-3 py-2">{{ k.bagian }}</td>
                            <td class="border px-3 py-2">{{ k.unit_kerja }}</td>
                            <td class="border px-3 py-2">
                                {{ k.posisi_jabatan }}
                            </td>
                            <td class="border px-3 py-2">
                                {{ k.klinis_non_klinis }}
                            </td>
                            <td class="border px-3 py-2">
                                {{ k.jenis_kelamin }}
                            </td>
                        </tr>

                        <tr v-if="karyawanFiltered.length === 0">
                            <td
                                colspan="9"
                                class="py-3 text-center text-gray-500"
                            >
                                Tidak ada data sesuai filter
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
