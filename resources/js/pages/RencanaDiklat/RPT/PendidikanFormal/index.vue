<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import HeaderMenu from '@/components/HeaderMenu.vue';
import { toast } from 'vue3-toastify';

// Definisikan tipe data untuk program dan baris tabel
interface DiklatRow {
    id: number;
    jenisPendidikan: string;
    kualifikasiPeserta: string;
    peserta: string;
    jumlah: number | null;
    periode: string;
    biaya: number | null;
}

interface DiklatProgram {
    id: number;
    nama_program: string;
    kategori: string;
    tahun: string;
    rows: DiklatRow[];
}

const props = defineProps<{
    programs: {
        id: number;
        nama_program: string;
        kategori: string;
        tahun: string;
    }[];
}>();

// Isi state awal dari props
const programs = ref<DiklatProgram[]>(
    props.programs.map(p => ({
        id: p.id,
        nama_program: p.nama_program,
        kategori: p.kategori,
        tahun: p.tahun || new Date().getFullYear().toString(),
        rows: [] 
    }))
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Rencana Program Tahunan',
        href: '/rencana-diklat',
    },
    {
        title: 'Pendidikan Formal',
        href: '/pendidikan-formal',
    },
];

// State untuk mengelola program


// State untuk modal tambah program
const isModalOpen = ref(false);
const newProgram = ref({
    name: '',
    category: 'Formal', // Default value
    customCategory: '',
    year: new Date().getFullYear().toString(),
});


// --- Methods ---

// Membuka modal dan mereset form
const openAddProgramModal = () => {
    isModalOpen.value = true;
    newProgram.value = {
        name: '',
        category: 'Formal',
        customCategory: '',
        year: new Date().getFullYear().toString(),
    };
};

// Menutup modal
const closeAddProgramModal = () => {
    isModalOpen.value = false;
};

const form = useForm({
    nama_program:'',
    kategori:'',
    tahun:''
})
// Menambahkan program baru
const addProgram = () => {
    const finalCategory = newProgram.value.category === 'Lainnya' 
        ? newProgram.value.customCategory 
        : newProgram.value.category;

    if (!newProgram.value.name || !finalCategory) {
        toast.error('Nama program dan kategori harus diisi!');
        return;
    }

    // Isi form dengan data dari modal
    form.nama_program = newProgram.value.name;
    form.kategori = finalCategory;
    form.tahun = newProgram.value.year;

    form.post('/RencanaDiklat/RPT/PF/store', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Data Berhasil Ditambah!');
            closeAddProgramModal();
            // Opsional: refresh halaman agar data baru muncul
            // Tapi lebih baik: tambahkan langsung ke `programs.value`
        },
    });
};

// Menambahkan baris baru ke tabel program tertentu
const addRow = (programIndex: number) => {
    const newRow: DiklatRow = {
        id: Date.now(),
        jenisPendidikan: '',
        kualifikasiPeserta: '',
        peserta: '',
        jumlah: null,
        periode: '',
        biaya: null,
    };
    programs.value[programIndex].rows.push(newRow);
};

// Menghapus baris dari tabel
const deleteRow = (programIndex: number, rowIndex: number) => {
    programs.value[programIndex].rows.splice(rowIndex, 1);
};

// Menghapus seluruh program
const deleteProgram = (id:number) => {
    if(!id) return;
    
    const confir = window.confirm('Apakah anda yakin ingin menghapus?');

    if(confir){
        router.delete(`/RencanaDiklat/RPT/PF/delete/${id}`,{
            onSuccess: () =>
            toast.success('Data Berhasil dihapus!')
        });
    }
};

// Menu
const page = usePage()
const auth = page.props.auth
const rawRole = auth.user?.role || []
const roles = Array.isArray(rawRole) ? rawRole : [rawRole]

const menuItems = [
  ...(roles.includes('admin_kesra') || roles.includes('teknis')
    ? [{ title: 'Data Lembur', href: '/karyawan' }]
    : []),

  ...(roles.includes('admin_kesra')
    ? [{ title: 'Gaji Karyawan', href: '/gaji' }]
    : []),

  { title: 'Pendidikan Formal', href: '/RencanaDiklat/RPT/PF' },
  { title: 'Pendidikan Non Formal', href: '/RencanaDiklat/RPT/PN' },
  { title: 'HLC', href: '/HLC/Home/manajemen' },
  { title: 'Jadwal Non Formal', href: '/RencanaDiklat/jadwal' },
//   { title: 'Eksternal', href: '/RencanaDiklat/RPT/PF' },
]
</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />
        <div class="p-6">
            <!-- Tab Navigation -->
            <!-- Tombol Utama -->
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Pendidikan Formal</h1>
                <button
                    @click="openAddProgramModal"
                    class="px-2 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Pendidikan
                </button>
            </div>

            <!-- Daftar Program / "Rumah" -->
            <div v-if="programs.length > 0" class="space-y-8">
                <div
                    v-for="(program, programIndex) in programs"
                    :key="program.id"
                    class="bg-white p-6 rounded-xl shadow-md border border-gray-200"
                >
                    <!-- Header Program -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ program.nama_program }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Kategori: <span class="font-medium text-gray-700">{{ program.kategori }}</span> | Tahun: <span class="font-medium text-gray-700">{{ program.tahun }}</span>
                            </p>
                        </div>
                        <button
                            @click="deleteProgram(program.id)"
                            class="mt-4 sm:mt-0 text-red-600 hover:text-red-800 font-medium text-sm"
                        >
                            Hapus Program
                        </button>
                    </div>

                    <!-- Tombol Tambah Baris -->
                    <div class="mb-4">
                        <button
                            @click="addRow(programIndex)"
                            class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Diklat
                        </button>
                    </div>

                    <!-- Tabel -->
                    <div v-if="program.rows.length > 0" class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pendidikan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kualifikasi Peserta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JML</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(row, rowIndex) in program.rows" :key="row.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ rowIndex + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <input type="text" v-model="row.jenisPendidikan" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" v-model="row.kualifikasiPeserta" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" v-model="row.peserta" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" v-model.number="row.jumlah" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" v-model="row.periode" placeholder="contoh: Januari 2024" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" v-model.number="row.biaya" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="deleteRow(programIndex, rowIndex)" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pesan jika tabel kosong -->
                    <div v-else class="text-center py-8 text-gray-500">
                        Belum ada data diklat. Klik "Tambah Diklat" untuk mengisi tabel.
                    </div>
                </div>
            </div>

            <!-- Pesan jika belum ada program sama sekali -->
            <div v-else class="text-center py-16 bg-white rounded-xl shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Program Diklat</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambah program pendidikan baru.</p>
            </div>
        </div>

        <!-- Modal Tambah Program -->
<div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
  <!-- Background Overlay (klik di sini untuk tutup) -->
  <div 
    class="fixed inset-0 backdrop-blur-md bg-opacity-50 transition-opacity" 
    @click="closeAddProgramModal"
  ></div>

  <!-- Modal Container -->
  <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
    <div 
      class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
      @click.stop 
    >
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-900">Tambah Program Pendidikan Baru</h3>
        </div>
        <div class="space-y-4">
          <div>
            <label for="program-name" class="block text-sm font-medium text-gray-700">Nama Program</label>
            <input
              type="text"
              id="program-name"
              v-model="newProgram.name"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="contoh: Diklat Kepemimpinan"
            />
          </div>
          <div>
            <label for="program-category" class="block text-sm font-medium text-gray-700">Kategori</label>
            <select
              id="program-category"
              v-model="newProgram.category"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="Formal">Formal</option>
              <option value="Non Formal">Non Formal</option>
              <option value="Lainnya">Lainnya (Custom)</option>
            </select>
          </div>
          <div v-if="newProgram.category === 'Lainnya'">
            <label for="custom-category" class="block text-sm font-medium text-gray-700">Kategori Kustom</label>
            <input
              type="text"
              id="custom-category"
              v-model="newProgram.customCategory"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Masukkan kategori"
            />
          </div>
          <div>
            <label for="program-year" class="block text-sm font-medium text-gray-700">Tahun</label>
            <input
              type="text"
              id="program-year"
              v-model="newProgram.year"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
        <button
          @click="addProgram"
          type="button"
          class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
        >
          Simpan
        </button>
        <button
          @click="closeAddProgramModal"
          type="button"
          class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm"
        >
          Batal
        </button>
      </div>
    </div>
  </div>
</div>
    </AppLayout>
</template>