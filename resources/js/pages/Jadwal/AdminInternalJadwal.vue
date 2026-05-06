<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';
import { toast } from 'vue3-toastify';

// --- Interface & Props ---
interface Peserta {
    id: number;
    bagian: string;
    nrp: string;
}

interface Jadwal {
    id: number;
    tanggal?: string;
    tanggal_mulai?: string;
    nama_kegiatan?: string;
    nama_diklat?: string;
    nama_pengajar?: string;
    penyelenggara?: string;
    lokasi?: string;
    peserta?: Peserta[];
    hlc?: any[];
    eksternal?: any[];
}

const props = defineProps<{
    jadwalInternal: Jadwal[];
    jadwalHLC: any[];
    jadwalEksternal: any[];
    filters: { search: string };
    templates: any[];
}>();

// --- State ---
const activeTab = ref('internal');
const search = ref(props.filters.search || '');

const menuItems = [
    { title: 'Internal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Eksternal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
];

// --- Functions ---
const formatDate = (date: string) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

// Debounced Search
const performSearch = debounce((value: string) => {
    router.get(
        '/Jadwal',
        { search: value },
        { preserveState: true, replace: true },
    );
}, 500);

watch(search, (newValue) => {
    performSearch(newValue);
});

function goHP() {
    router.get('/NoHP');
}

// panggil whattsapp
const selectedTemplate = ref(
    props.templates.length > 0 ? props.templates[0].slug : '',
);
const kirimNotifikasi = (id: number, tipe: string) => {
    if (!selectedTemplate.value) {
        alert('Silakan pilih template terlebih dahulu!');
        return;
    }

    if (
        confirm(
            `Kirim notifikasi menggunakan template "${selectedTemplate.value}"?`,
        )
    ) {
        router.post(route('jadwal.send-wa'), {
            id: id,
            tipe: tipe,
            template_slug: selectedTemplate.value,
        },{
            onSuccess: () => {
                toast.success('Notifikasi Berhasil dikirim!');
            },
        });
    }
};
</script>

<template>
    <Head title="Jadwal Diklat Saya" />

    <AppLayout>
        <div class="space-y-4 p-4 md:space-y-6 md:p-6 lg:p-8">
            <!-- Header Section -->
            <div class="flex flex-col gap-1">
                <h1
                    class="text-xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-white"
                >
                    Jadwal Diklat Saya
                </h1>
                <p
                    class="text-xs text-slate-500 md:text-sm dark:text-slate-400"
                >
                    Daftar seluruh pelatihan mendatang yang Anda ikuti.
                </p>
            </div>

            <!-- Search & Tabs Container -->
            <div
                class="sticky top-0 z-10 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:relative dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="relative w-full">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari kegiatan..."
                        class="h-10 w-full rounded-xl border border-slate-300 bg-slate-50 pr-4 pl-10 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800"
                    />
                </div>
                <div
                    class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/50 p-4 md:flex-row md:items-center"
                >
                    <label
                        class="text-xs font-bold tracking-widest text-slate-500 uppercase"
                        >Pilih Template Pesan:</label
                    >
                    <select
                        v-model="selectedTemplate"
                        class="h-9 rounded-lg border-slate-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500/20 md:w-64"
                    >
                        <option value="" disabled>-- Pilih Template --</option>
                        <option
                            v-for="temp in templates"
                            :key="temp.id"
                            :value="temp.slug"
                        >
                            {{ temp.nama_template }}
                        </option>
                    </select>
                    <p class="text-[10px] text-blue-500 italic">
                        *Pilih template terlebih dahulu sebelum klik 'Umumkan
                        WA'
                    </p>
                </div>
                <!-- Horizontal Tabs: Scrollable on Mobile -->
                <div
                    class="no-scrollbar flex gap-2 overflow-x-auto border-t border-slate-100 pt-3 pb-1 dark:border-slate-800"
                >
                    <button
                        v-for="tab in ['internal', 'hlc', 'eksternal']"
                        :key="tab"
                        @click="activeTab = tab"
                        :class="[
                            'flex-1 rounded-lg px-4 py-2 text-xs font-bold whitespace-nowrap capitalize transition-all sm:flex-none',
                            activeTab === tab
                                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800',
                        ]"
                    >
                        {{ tab }}
                    </button>
                </div>
            </div>
            <button
                @click="goHP"
                class="top-1/2 right-2 -translate-y-1/2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-2"
            >
                Tambah Nomor HP
            </button>

            <!-- Content Area -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <!-- 1. JADWAL INTERNAL -->
                <div v-if="activeTab === 'internal'">
                    <!-- Desktop Table -->
                    <div class="hidden overflow-x-auto md:block">
                        <table
                            v-if="jadwalInternal.length"
                            class="w-full text-left text-sm"
                        >
                            <thead
                                class="bg-slate-50 text-[10px] font-bold tracking-widest text-slate-500 uppercase dark:bg-slate-800/50"
                            >
                                <tr>
                                    <th class="px-6 py-4">Nama Kegiatan</th>
                                    <th class="px-6 py-4">Pengajar</th>
                                    <th class="px-6 py-4">Lokasi</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <tr
                                    v-for="item in jadwalInternal"
                                    :key="item.id"
                                    class="transition-colors hover:bg-slate-50"
                                >
                                    <td
                                        class="px-6 py-4 font-bold text-blue-600"
                                    >
                                        {{ item.nama_kegiatan }}
                                    </td>
                                    <td class="px-6 py-4 font-medium">
                                        {{ item.nama_pengajar }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ item.lokasi }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ formatDate(item.tanggal!) }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        <button
                                            @click="
                                                kirimNotifikasi(
                                                    item.id,
                                                    'internal',
                                                )
                                            "
                                            class="flex items-center gap-2 rounded-lg bg-green-500 px-3 py-1.5 text-xs text-white shadow-sm transition hover:bg-green-600"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                                />
                                            </svg>
                                            Umumkan WA
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile Card View -->
                    <div
                        class="divide-y divide-slate-100 md:hidden dark:divide-slate-800"
                    >
                        <div
                            v-for="item in jadwalInternal"
                            :key="item.id"
                            class="space-y-3 p-4"
                        >
                            <h4 class="leading-tight font-bold text-blue-600">
                                {{ item.nama_kegiatan }}
                            </h4>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <p
                                        class="text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Pengajar
                                    </p>
                                    <p class="font-medium dark:text-slate-300">
                                        {{ item.nama_pengajar }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Lokasi
                                    </p>
                                    <p class="font-medium dark:text-slate-300">
                                        {{ item.lokasi }}
                                    </p>
                                </div>
                                <div
                                    class="col-span-2 border-t border-slate-50 pt-1"
                                >
                                    <p
                                        class="text-[10px] font-bold text-slate-400 uppercase"
                                    >
                                        Tanggal Pelaksanaan
                                    </p>
                                    <p
                                        class="font-mono text-slate-900 dark:text-white"
                                    >
                                        {{ formatDate(item.tanggal!) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <EmptyState v-if="!jadwalInternal.length" />
                </div>

                <!-- 2. JADWAL HLC (Logika Serupa untuk Mobile Card) -->
                <div v-if="activeTab === 'hlc'">
                    <div class="hidden overflow-x-auto md:block">
                        <table
                            v-if="jadwalHLC.length"
                            class="w-full text-left text-sm"
                        >
                            <thead
                                class="bg-slate-50 text-[10px] font-bold tracking-widest text-slate-500 uppercase dark:bg-slate-800/50"
                            >
                                <tr>
                                    <th class="px-6 py-4 text-emerald-600">
                                        Nama Diklat
                                    </th>
                                    <th class="px-6 py-4">Program</th>
                                    <th class="px-6 py-4">Mulai</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <template
                                    v-for="prog in jadwalHLC"
                                    :key="prog.id"
                                >
                                    <tr
                                        v-for="hlc in prog.hlc"
                                        :key="hlc.id"
                                        class="transition-colors hover:bg-slate-50"
                                    >
                                        <td
                                            class="px-6 py-4 font-bold text-emerald-600"
                                        >
                                            {{ hlc.nama_diklat }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-slate-500 italic"
                                        >
                                            {{ prog.nama_program }}
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs">
                                            {{ formatDate(hlc.tanggal_mulai) }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile Card View -->
                    <div
                        class="divide-y divide-slate-100 md:hidden dark:divide-slate-800"
                    >
                        <template v-for="prog in jadwalHLC" :key="prog.id">
                            <div
                                v-for="hlc in prog.hlc"
                                :key="hlc.id"
                                class="space-y-2 p-4"
                            >
                                <h4
                                    class="leading-tight font-bold text-emerald-600"
                                >
                                    {{ hlc.nama_diklat }}
                                </h4>
                                <p class="text-[10px] text-slate-500 italic">
                                    {{ prog.nama_program }}
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <svg
                                        class="h-3.5 w-3.5 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            stroke-width="2"
                                        />
                                    </svg>
                                    <span class="font-mono text-xs font-bold">{{
                                        formatDate(hlc.tanggal_mulai)
                                    }}</span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <EmptyState v-if="!jadwalHLC.length" />
                </div>

                <!-- 3. JADWAL EKSTERNAL (Logika Serupa untuk Mobile Card) -->
                <div v-if="activeTab === 'eksternal'">
                    <div class="hidden overflow-x-auto md:block">
                        <table
                            v-if="jadwalEksternal.length"
                            class="w-full text-left text-sm"
                        >
                            <thead
                                class="bg-slate-50 text-[10px] font-bold tracking-widest text-slate-500 uppercase dark:bg-slate-800/50"
                            >
                                <tr>
                                    <th class="px-6 py-4 text-purple-600">
                                        Nama Diklat
                                    </th>
                                    <th class="px-6 py-4">Penyelenggara</th>
                                    <th class="px-6 py-4">Mulai</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <template
                                    v-for="prog in jadwalEksternal"
                                    :key="prog.id"
                                >
                                    <tr
                                        v-for="eks in prog.eksternal"
                                        :key="eks.id"
                                        class="transition-colors hover:bg-slate-50"
                                    >
                                        <td
                                            class="px-6 py-4 font-bold text-purple-600"
                                        >
                                            {{ eks.nama_diklat }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ eks.penyelenggara }}
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs">
                                            {{ formatDate(eks.tanggal_mulai) }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile Card View -->
                    <div
                        class="divide-y divide-slate-100 md:hidden dark:divide-slate-800"
                    >
                        <template
                            v-for="prog in jadwalEksternal"
                            :key="prog.id"
                        >
                            <div
                                v-for="eks in prog.eksternal"
                                :key="eks.id"
                                class="space-y-2 p-4"
                            >
                                <h4
                                    class="leading-tight font-bold text-purple-600"
                                >
                                    {{ eks.nama_diklat }}
                                </h4>
                                <div
                                    class="mt-2 flex items-center justify-between"
                                >
                                    <span
                                        class="text-xs font-medium text-slate-500"
                                        >{{ eks.penyelenggara }}</span
                                    >
                                    <span
                                        class="rounded bg-purple-50 px-2 py-0.5 font-mono text-xs font-bold text-purple-600"
                                        >{{
                                            formatDate(eks.tanggal_mulai)
                                        }}</span
                                    >
                                </div>
                            </div>
                        </template>
                    </div>
                    <EmptyState v-if="!jadwalEksternal.length" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Menghilangkan scrollbar pada tab horizontal mobile */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
