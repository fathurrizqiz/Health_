

Tentu, ini adalah revisi kode berdasarkan rekomendasi saya. Saya akan menggabungkan **Dashboard Statistik**, **Filter Tab**, dan **Tampilan Kartu/Grid Modern** untuk menciptakan antarmuka yang lebih intuitif dan menarik.

Desain ini lebih cocok karena:
*   **Untuk Admin:** Dashboard statistik memberikan gambaran cepat, dan tampilan kartu memudahkan mengelola setiap kelompok jadwal.
*   **Untuk Peserta:** Tampilan yang lebih visual dan terfilter (berdasarkan tab) membuat informasi jadwal lebih mudah dipahami.

---

### Kode yang Direvisi

```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import HeaderMenu from '@/components/HeaderMenu.vue';
import { ref, computed } from 'vue';

// --- Breadcrumbs dan Menu (dari kode Anda) ---
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rencana Program Tahunan', href: '/rencana-diklat' },
    { title: 'Jadwal Diklat', href: '/jadwal-diklat' },
];

const page = usePage()
const auth = page.props.auth
const rawRole = auth.user?.role || []
const roles = Array.isArray(rawRole) ? rawRole : [rawRole]

const menuItems = [
    { title: 'Pendidikan Formal', href: '/RencanaDiklat/RPT/PF' },
    { title: 'Pendidikan Non Formal', href: '/RencanaDiklat/RPT/PN' },
    { title: 'HLC', href: '/HLC/Home/manajemen' },
    { title: 'Jadwal Non Formal', href: '/RencanaDiklat/jadwal' },
//   { title: 'Eksternal', href: '/RencanaDiklat/RPT/PF' },
]

// --- State dan Data untuk Jadwal ---
interface JadwalItem {
    no: number;
    namaDiklat: string;
    tanggal: string;
    jam: string;
    pengajar: string;
    status: 'Selesai' | 'Berlangsung' | 'Akan Datang';
}

interface ScheduleGroup {
    id: number;
    judul: string;
    bulan: string;
    tahun: number;
    jadwals: JadwalItem[];
}

const scheduleGroups = ref<ScheduleGroup[]>([
    {
        id: 1,
        judul: 'Jadwal Diklat Pendidikan Formal',
        bulan: 'Januari',
        tahun: 2024,
        jadwals: [
            { no: 1, namaDiklat: 'Kepemimpinan Level 1', tanggal: '10 Jan 2024', jam: '09:00 - 12:00', pengajar: 'Dr. Budi Santoso, S.H., M.H.', status: 'Selesai' },
            { no: 2, namaDiklat: 'Manajemen Proyek', tanggal: '15 Jan 2024', jam: '13:00 - 16:00', pengajar: 'Andi Wijaya, S.T., M.T.', status: 'Selesai' },
            { no: 3, namaDiklat: 'Etika Profesi', tanggal: '25 Jan 2024', jam: '09:00 - 12:00', pengajar: 'Siti Nurhaliza, S.Psi., M.Psi.', status: 'Berlangsung' },
        ]
    },
    {
        id: 2,
        judul: 'Jadwal Diklat Pendidikan Non Formal',
        bulan: 'Februari',
        tahun: 2024,
        jadwals: [
            { no: 1, namaDiklat: 'Public Speaking', tanggal: '5 Feb 2024', jam: '09:00 - 11:00', pengajar: 'Rina Amelia, S.Kom., M.M.', status: 'Akan Datang' },
            { no: 2, namaDiklat: 'Digital Marketing', tanggal: '12 Feb 2024', jam: '13:00 - 15:00', pengajar: 'Fajar Pratama, S.E., M.Kom.', status: 'Akan Datang' },
        ]
    }
]);

// --- State untuk Modal Tambah Kelompok Jadwal ---
const showAddModal = ref(false);
const newJudul = ref('');
const newBulan = ref('');
const newTahun = ref(new Date().getFullYear());

// --- State untuk Modal Tambah Item Baru ---
const showAddRowModal = ref(false);
const activeGroupId = ref<number | null>(null);
const newRowNamaDiklat = ref('');
const newRowTanggal = ref('');
const newRowJam = ref('');
const newRowPengajar = ref('');

// --- State untuk Pencarian dan Filter ---
const searchQuery = ref('');
const activeTab = ref('all'); // 'all', 'upcoming', 'ongoing', 'completed'

// --- Computed Properties untuk Statistik ---
const totalSchedules = computed(() => scheduleGroups.value.reduce((total, group) => total + group.jadwals.length, 0));
const completedSchedules = computed(() => scheduleGroups.value.reduce((total, group) => total + group.jadwals.filter(item => item.status === 'Selesai').length, 0));
const ongoingSchedules = computed(() => scheduleGroups.value.reduce((total, group) => total + group.jadwals.filter(item => item.status === 'Berlangsung').length, 0));
const upcomingSchedules = computed(() => scheduleGroups.value.reduce((total, group) => total + group.jadwals.filter(item => item.status === 'Akan Datang').length, 0));

// --- Computed Property untuk Pencarian dan Filter Gabungan ---
const filteredScheduleGroups = computed(() => {
    let groups = scheduleGroups.value;

    // 1. Filter berdasarkan Tab Status
    if (activeTab.value !== 'all') {
        const statusMap: { [key: string]: JadwalItem['status'] } = {
            'upcoming': 'Akan Datang',
            'ongoing': 'Berlangsung',
            'completed': 'Selesai'
        };
        const targetStatus = statusMap[activeTab.value];
        groups = groups.map(group => {
            const filteredJadwals = group.jadwals.filter(item => item.status === targetStatus);
            if (filteredJadwals.length > 0) {
                return { ...group, jadwals: filteredJadwals };
            }
            return null;
        }).filter(group => group !== null) as ScheduleGroup[];
    }

    // 2. Filter berdasarkan Query Pencarian
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        groups = groups.map(group => {
            const filteredJadwals = group.jadwals.filter(item =>
                item.namaDiklat.toLowerCase().includes(query) ||
                item.pengajar.toLowerCase().includes(query)
            );
            if (filteredJadwals.length > 0) {
                return { ...group, jadwals: filteredJadwals };
            }
            return null;
        }).filter(group => group !== null) as ScheduleGroup[];
    }

    return groups;
});

// --- Fungsi untuk Kelompok Jadwal ---
const openAddModal = () => {
    showAddModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
    newJudul.value = '';
    newBulan.value = '';
    newTahun.value = new Date().getFullYear();
};

const addScheduleGroup = () => {
    if (newJudul.value && newBulan.value && newTahun.value) {
        const newGroup: ScheduleGroup = {
            id: Date.now(),
            judul: newJudul.value,
            bulan: newBulan.value,
            tahun: newTahun.value,
            jadwals: []
        };
        scheduleGroups.value.push(newGroup);
        closeAddModal();
    }
};

// --- Fungsi untuk Tambah Item Baru ---
const openAddRowModal = (groupId: number) => {
    activeGroupId.value = groupId;
    showAddRowModal.value = true;
};

const closeAddRowModal = () => {
    showAddRowModal.value = false;
    activeGroupId.value = null;
    newRowNamaDiklat.value = '';
    newRowTanggal.value = '';
    newRowJam.value = '';
    newRowPengajar.value = '';
};

const addNewRow = () => {
    if (activeGroupId.value === null) return;

    const group = scheduleGroups.value.find(g => g.id === activeGroupId.value);
    if (group) {
        const newItem: JadwalItem = {
            no: group.jadwals.length + 1,
            namaDiklat: newRowNamaDiklat.value,
            tanggal: newRowTanggal.value,
            jam: newRowJam.value,
            pengajar: newRowPengajar.value,
            status: 'Akan Datang',
        };
        group.jadwals.push(newItem);
        closeAddRowModal();
    }
};

</script>

<template>
    <Head title="Jadwal Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />
        
        <div class="p-6 bg-gray-50 min-h-screen">
            <!-- Header Halaman -->
            <div class="mb-6">
                <h2 class="font-bold text-3xl text-gray-800">Jadwal Diklat</h2>
                <p class="text-gray-600">Kelola dan pantau seluruh jadwal pelatihan.</p>
            </div>

            <!-- Dashboard Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Jadwal</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalSchedules }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Akan Datang</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ upcomingSchedules }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Berlangsung</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ ongoingSchedules }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Selesai</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ completedSchedules }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontrol: Pencarian dan Tambah -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari nama diklat atau pengajar..." 
                    class="shadow-sm border border-gray-300 rounded-lg w-full sm:w-96 py-2.5 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition duration-300 ease-in-out w-full sm:w-auto">
                    + Tambah Jadwal Baru
                </button>
            </div>

            <!-- Filter Tab -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'all'" :class="[activeTab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                        Semua
                    </button>
                    <button @click="activeTab = 'upcoming'" :class="[activeTab === 'upcoming' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                        Akan Datang
                    </button>
                    <button @click="activeTab = 'ongoing'" :class="[activeTab === 'ongoing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                        Berlangsung
                    </button>
                    <button @click="activeTab = 'completed'" :class="[activeTab === 'completed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                        Selesai
                    </button>
                </nav>
            </div>

            <!-- Tampilan Utama: Grid Kartu -->
            <div v-if="filteredScheduleGroups.length === 0" class="text-center py-12 bg-white rounded-xl shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal</h3>
                <p class="mt-1 text-sm text-gray-500">{{ searchQuery ? 'Tidak ada jadwal yang cocok dengan pencarian Anda.' : 'Mulai dengan menambah jadwal baru.' }}</p>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div v-for="group in filteredScheduleGroups" :key="group.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Header Kartu -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-5">
                        <h3 class="text-white font-bold text-lg">{{ group.judul }}</h3>
                        <p class="text-blue-100 text-sm mt-1">{{ group.bulan }} {{ group.tahun }}</p>
                    </div>
                    
                    <!-- Daftar Item dalam Kartu -->
                    <div class="p-5 space-y-3 max-h-96 overflow-y-auto">
                        <div v-for="item in group.jadwals" :key="item.no" class="flex items-start justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ item.namaDiklat }}</h4>
                                <div class="mt-1 text-xs text-gray-600 space-y-1">
                                    <p class="flex items-center"><svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>{{ item.tanggal }}</p>
                                    <p class="flex items-center"><svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>{{ item.jam }}</p>
                                    <p class="flex items-center"><svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>{{ item.pengajar }}</p>
                                </div>
                            </div>
                            <span :class="{
                                'px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap': true,
                                'bg-green-100 text-green-800': item.status === 'Selesai',
                                'bg-yellow-100 text-yellow-800': item.status === 'Berlangsung',
                                'bg-blue-100 text-blue-800': item.status === 'Akan Datang'
                            }">
                                {{ item.status }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Footer Kartu -->
                    <div class="px-5 pb-5">
                        <button @click="openAddRowModal(group.id)" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            + Tambah Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk Tambah Kelompok Jadwal -->
        <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeAddModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Jadwal Baru</h3>
                    <form @submit.prevent="addScheduleGroup">
                        <div class="mb-4">
                            <label for="judul" class="block text-gray-700 text-sm font-bold mb-2">Judul Jadwal</label>
                            <input v-model="newJudul" type="text" id="judul" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Diklat Teknis" required>
                        </div>
                        <div class="mb-4">
                            <label for="bulan" class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                            <input v-model="newBulan" type="text" id="bulan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Maret" required>
                        </div>
                        <div class="mb-4">
                            <label for="tahun" class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                            <input v-model="newTahun" type="number" id="tahun" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="closeAddModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal untuk Tambah Item Baru -->
        <div v-if="showAddRowModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeAddRowModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Item Jadwal</h3>
                    <form @submit.prevent="addNewRow">
                        <div class="mb-4">
                            <label for="rowNamaDiklat" class="block text-gray-700 text-sm font-bold mb-2">Nama Diklat</label>
                            <input v-model="newRowNamaDiklat" type="text" id="rowNamaDiklat" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="rowTanggal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                            <input v-model="newRowTanggal" type="text" id="rowTanggal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Contoh: 20 Mar 2024" required>
                        </div>
                        <div class="mb-4">
                            <label for="rowJam" class="block text-gray-700 text-sm font-bold mb-2">Jam Diklat</label>
                            <input v-model="newRowJam" type="text" id="rowJam" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Contoh: 09:00 - 12:00" required>
                        </div>
                        <div class="mb-4">
                            <label for="rowPengajar" class="block text-gray-700 text-sm font-bold mb-2">Pengajar</label>
                            <input v-model="newRowPengajar" type="text" id="rowPengajar" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="closeAddRowModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
```