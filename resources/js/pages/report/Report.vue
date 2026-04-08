<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Laporan Diklat', href: 'laporan.diklat' },
];

interface Filters {
    months: number[];
    year: number;
    bagian: string;
}

interface TargetKategori {
    kategori: string;
    totalKaryawan: number;
    targetPerOrang: number;
    totalTargetJam: number;
    aktualJam: number;
    persentase: number;
}

const props = defineProps<{
    filters: Filters;
    totalPerKategori: TargetKategori[];
    totalJamDiklat: number;
    targetAll: number;
}>();

// Daftar bulan untuk Checkbox
const listBulan = [
    { id: 1, nama: 'Januari' },
    { id: 2, nama: 'Februari' },
    { id: 3, nama: 'Maret' },
    { id: 4, nama: 'April' },
    { id: 5, nama: 'Mei' },
    { id: 6, nama: 'Juni' },
    { id: 7, nama: 'Juli' },
    { id: 8, nama: 'Agustus' },
    { id: 9, nama: 'September' },
    { id: 10, nama: 'Oktober' },
    { id: 11, nama: 'November' },
    { id: 12, nama: 'Desember' },
];

// State untuk menyimpan pilihan checkbox
const selectedMonths = ref<number[]>(props.filters.months);

// Action untuk memicu reload data ke Backend
const applyFilter = () => {
    if (selectedMonths.value.length === 0) {
        alert('Pilih minimal 1 bulan!');
        return;
    }

    // Ganti route url ini dengan name route kamu
    router.get(
        route('laporan.diklat'),
        { months: selectedMonths.value,
            // year: props.filters.year,
            bagian: searchBagian.value,
         },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Helper periode
const teksPeriode = computed(() => {
    if (selectedMonths.value.length === 0) return 'Pilih Bulan';
    const namaBulanTerpilih = selectedMonths.value
        .sort((a, b) => a - b) // Urutkan bulan dari awal ke akhir
        .map((m) => listBulan.find((b) => b.id === m)?.nama)
        .join(', ');

    return `${namaBulanTerpilih} ${props.filters.year}`;
});

const persentaseTotal = computed(() => {
    if (props.targetAll <= 0) return 0;
    return ((props.totalJamDiklat / props.targetAll) * 100).toFixed(2);
});

// const selectedMonths = ref<number[]>(props.filters.months);
const searchBagian = ref<string>(props.filters.bagian || ''); 

// grafik
const chartSeries = computed(() => {
    return [
        {
            name: 'Target Jam',
            data: props.totalPerKategori.map(item => item.totalTargetJam)
        },
        {
            name: 'Aktual Jam',
            data: props.totalPerKategori.map(item => item.aktualJam)
        }
    ];
});

// Opsi tampilan grafik
const chartOptions = computed(() => {
    return {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }, // Sembunyikan menu download bawaan chart agar bersih
            fontFamily: 'inherit',
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4, // Bikin ujung batangnya agak membulat (Tailwind style)
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: props.totalPerKategori.map(item => item.kategori), // Label bawah ambil dari nama bagian
            labels: {
                style: { colors: '#6B7280' }
            }
        },
        yaxis: {
            title: { text: 'Total Jam', style: { color: '#6B7280' } }
        },
        fill: { opacity: 1 },
        colors: ['#E5E7EB', '#2563EB'], // Warna: Abu-abu (Target), Biru (Aktual)
        tooltip: {
            y: {
                formatter: function (val: number) {
                    return val + " Jam";
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };
});
</script>

<template>
    <Head title="Laporan Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 py-6 sm:px-6 lg:px-8">
            
            <div class=" flex justify-end gap-3 sm:w-auto">
                <input
                    type="text"
                    v-model="searchBagian"
                    @keyup.enter="applyFilter"
                    placeholder="Cari nama bagian..."
                    class="w-full rounded-md justify-between border-gray-300 text-sm shadow-sm p-2 focus:border-blue-500 focus:ring-blue-500 sm:w-64"
                />
                <button
                    @click="applyFilter"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium whitespace-nowrap text-white transition-colors hover:bg-blue-700"
                >
                    Terapkan
                </button>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Laporan Diklat Karyawan
                    </h2>
                    <p class="mt-1 text-gray-500">
                        Periode:
                        <span class="font-medium text-gray-700">{{
                            teksPeriode
                        }}</span>
                    </p>
                </div>
            </div>

            <!-- AREA FILTER BULAN -->
            <div
                class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm"
            >
                <div
                    class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <h3 class="font-semibold text-gray-800">Filter Bulan</h3>
                    <button
                        @click="applyFilter"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    >
                        Terapkan Filter
                    </button>
                </div>
                <div
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                >
                    <label
                        v-for="bulan in listBulan"
                        :key="bulan.id"
                        class="flex cursor-pointer items-center space-x-2 rounded-lg border border-transparent p-2 transition-all select-none hover:border-gray-200 hover:bg-gray-50"
                    >
                        <input
                            type="checkbox"
                            :value="bulan.id"
                            v-model="selectedMonths"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm text-gray-700">{{
                            bulan.nama
                        }}</span>
                    </label>
                </div>
                <p
                    v-if="selectedMonths.length === 0"
                    class="mt-2 text-xs text-red-500"
                >
                    Setidaknya satu bulan harus dipilih.
                </p>
            </div>

            

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Card Target -->
                <div
                    class="flex flex-col justify-center rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
                >
                    <span class="text-sm font-medium text-gray-500"
                        >Total Target Jam (Periode Terpilih)</span
                    >
                    <span class="mt-2 text-3xl font-bold text-gray-800"
                        >{{ targetAll }}
                        <span class="text-base font-normal text-gray-500"
                            >Jam</span
                        ></span
                    >
                </div>

                <!-- Card Aktual -->
                <div
                    class="flex flex-col justify-center rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
                >
                    <span class="text-sm font-medium text-gray-500"
                        >Total Aktual Jam (Periode Terpilih)</span
                    >
                    <span class="mt-2 text-3xl font-bold text-blue-600"
                        >{{ totalJamDiklat }}
                        <span class="text-base font-normal text-gray-500"
                            >Jam</span
                        ></span
                    >
                </div>

                <!-- Card Persentase -->
                <div
                    class="flex flex-col justify-center rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
                >
                    <span class="text-sm font-medium text-gray-500"
                        >Pencapaian Total</span
                    >
                    <div class="mt-2 flex items-end gap-2">
                        <span class="text-3xl font-bold text-emerald-600"
                            >{{ persentaseTotal }}%</span
                        >
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm" v-if="totalPerKategori.length > 0">
                <h3 class="font-semibold text-gray-800 mb-2">Statistik Target vs Aktual Per Bagian</h3>
                <!-- Render komponen grafiknya di sini -->
                <VueApexCharts 
                    type="bar" 
                    height="350" 
                    :options="chartOptions" 
                    :series="chartSeries" 
                />
            </div>

            <!-- Tabel Detail Per Bagian (Sama seperti sebelumnya) -->
            <div
                class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm"
            >
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <h3 class="font-semibold text-gray-800">
                        Rincian Per Bagian
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead
                            class="bg-gray-50 text-xs text-gray-500 uppercase"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium">
                                    Bagian / Kategori
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-center font-medium"
                                >
                                    Total Karyawan
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right font-medium"
                                >
                                    Target/Orang
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right font-medium"
                                >
                                    Total Target
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right font-medium"
                                >
                                    Aktual Jam
                                </th>
                                <th
                                    scope="col"
                                    class="min-w-[200px] px-6 py-4 font-medium"
                                >
                                    Pencapaian
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in totalPerKategori"
                                :key="index"
                                class="border-b border-gray-50 transition-colors hover:bg-gray-50/50"
                            >
                                <td
                                    class="px-6 py-4 font-medium whitespace-nowrap text-gray-800"
                                >
                                    {{ item.kategori }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ item.totalKaryawan }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ item.targetPerOrang }} Jam
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ item.totalTargetJam }} Jam
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-medium text-blue-600"
                                >
                                    {{ item.aktualJam }} Jam
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-2.5 w-full rounded-full bg-gray-200"
                                        >
                                            <div
                                                class="h-2.5 rounded-full bg-emerald-500 transition-all duration-500"
                                                :style="{
                                                    width: `${item.persentase > 100 ? 100 : item.persentase}%`,
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            class="min-w-[3rem] text-xs font-semibold text-gray-600"
                                            >{{ item.persentase }}%</span
                                        >
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="totalPerKategori.length === 0">
                                <td
                                    colspan="6"
                                    class="px-6 py-8 text-center text-gray-400"
                                >
                                    Tidak ada data diklat untuk bulan terpilih.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
