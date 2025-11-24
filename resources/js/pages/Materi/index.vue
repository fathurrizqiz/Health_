<script setup lang="ts">
import HeaderMenu from '@/components/HeaderMenu.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';



interface MateriItem {
    id: number;
    title: string;
    type: 'folder' | 'file';
    status: string;
    file_path?: string | null;
    parent_id?: number | null;
}

// Props dari controller
const props = defineProps<{
    materi: MateriItem[];
    currentFolder: MateriItem | null;
    breadcrumb: MateriItem[];
}>();



// ✅ Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Library', href: '/dashboard' },
];

const page = usePage();
const auth = page.props.auth;
const rawRole = auth.user?.role || [];
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];
const menuItems = [
    { title: 'Perpustakaan Diklat', href: '/MateriDiklat/approve' },
    { title: 'Materi Ditolak', href: '/MateriDiklat/reject' },
];

// ✅ State
const materiList = ref(props.materi || []);
const isVerifyModalOpen = ref(false);
const isRejectModalOpen = ref(false);
const tambah = ref(false);

const selectedMateri = ref<any>(null);
const rejectionReason = ref('');

// Upload state
const uploadType = ref('');
const uploadTitle = ref('');
const uploadFile = ref<File | null>(null);



// 🔹 Upload materi baru atau folder
const handleUpload = () => {
  if (!uploadType.value) return alert('Pilih tipe dulu (Folder atau File)')
  if (!uploadTitle.value.trim()) return alert('Nama tidak boleh kosong')

  const formData = new FormData()
  formData.append('type', uploadType.value)
  formData.append('name', uploadTitle.value)
  
  // Tambahkan parent_id jika ada
  if (props.currentFolder) {
    formData.append('parent_id', props.currentFolder.id.toString())
  }

  if (uploadType.value === 'file' && uploadFile.value) {
    formData.append('file', uploadFile.value)
  }

  router.post('/Materi/store', formData, {
    forceFormData: true, // 🧩 WAJIB
    preserveScroll: true,
    onSuccess: () => {
      alert('✅ Materi berhasil ditambahkan!')
      tambah.value = false
      uploadType.value = ''
      uploadTitle.value = ''
      uploadFile.value = null
      location.reload();
      // Reload halaman untuk menampilkan data terbaru
      window.location.reload()
    },
    onError: () => {
      alert('❌ Gagal upload. Coba lagi.')
    },
  })
}

// 🔹 Navigasi ke folder
const navigateToFolder = (folderId: number) => {
    window.location.href = `/Materi/folder/${folderId}`;
};

// 🔹 Navigasi ke parent folder
const navigateToParent = () => {
    if (props.currentFolder && props.currentFolder.parent_id) {
        window.location.href = `/Materi/folder/${props.currentFolder.parent_id}`;
    } else {
        window.location.href = `/Materi`;
    }
};

// handle upload
// Pastikan definisinya ada di <script setup>

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target?.files?.length) {
        uploadFile.value = target.files[0];
    }
};


// 🔹 Buka modal verifikasi
const openVerifyModal = (item: any) => {
    selectedMateri.value = item;
    isVerifyModalOpen.value = true;
};

// 🔹 Verifikasi dokumen
const handleVerify = () => {
    router.put(
        `/Materi/verify/${selectedMateri.value.id}`,
        {},
        {
            onSuccess: () => {
                alert('✅ Dokumen diverifikasi.');
                isVerifyModalOpen.value = false;
                window.location.reload();
                location.reload();
            },
            onError: () => alert('Gagal memverifikasi dokumen.'),
        },
    );
};

// 🔹 Buka modal tolak
const openRejectModal = (item: any) => {
    selectedMateri.value = item;
    rejectionReason.value = '';
    isRejectModalOpen.value = true;
};

// 🔹 Submit alasan penolakan
const submitRejection = () => {
    if (!rejectionReason.value.trim())
        return alert('Alasan tidak boleh kosong.');

    router.put(
        `/Materi/reject/${selectedMateri.value.id}`,
        {
            reason: rejectionReason.value,
        },
        {
            onSuccess: () => {
                alert('❌ Dokumen ditolak.');
                isRejectModalOpen.value = false;
                window.location.reload();
            },
            onError: () => alert('Gagal menolak dokumen.'),
        },
    );
};

// 🔹 Hapus materi
const deleteMateri = (id: number) => {
    if (!confirm('Yakin ingin menghapus materi ini?')) return;

    router.delete(`/Materi/delete/${id}`, {
        onSuccess: () => {
            alert('🗑️ Materi dihapus.');
            window.location.reload();
        },
        onError: () => alert('Gagal menghapus materi.'),
    });
};
</script>

<template>
    <Head title="Perpustakaan Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <HeaderMenu :items="menuItems" />

        <div class="px-6 py-8">
            <div class="mx-auto max-w-7xl">

                <!-- HEADER -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Perpustakaan Diklat</h1>
                        <p class="text-gray-600">Kelola dan akses semua materi pelatihan</p>
                    </div>

                    <button
                        @click="tambah = true"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 shadow"
                    >
                        + Tambah Materi
                    </button>
                </div>

                <!-- BREADCRUMB -->
                <div class="flex items-center gap-2 text-sm mb-4">
                    <a href="/Materi" class="text-blue-600 hover:underline">Home</a>

                    <template v-for="(item, idx) in breadcrumb" :key="item.id">
                        <span>/</span>

                        <a
                            v-if="idx < breadcrumb.length - 1"
                            :href="`/Materi/folder/${item.id}`"
                            class="text-blue-600 hover:underline"
                        >
                            {{ item.title }}
                        </a>

                        <span
                            v-else
                            class="font-semibold text-gray-800"
                        >
                            {{ item.title }}
                        </span>
                    </template>
                </div>

                <!-- Back Button -->
                <button
                    v-if="currentFolder"
                    @click="navigateToParent"
                    class="mb-6 flex items-center text-blue-600 hover:underline"
                >
                    ← Kembali
                </button>

                <!-- GRID LIST -->
                <div v-if="materiList.length === 0" class="p-6 text-center text-gray-500">
                    Tidak ada materi di folder ini.
                </div>

                <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <div
                        v-for="item in materiList"
                        :key="item.id"
                        class="group p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition shadow-sm"
                    >

                        <!-- Icon -->
                        <div @click="item.type === 'folder' ? navigateToFolder(item.id) : null" class="flex flex-col items-center">
                            <svg v-if="item.type === 'folder'" class="h-12 w-12 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                            <!-- <svg  v-if="item.type === 'folder'"  xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check-icon lucide-file-check"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="m9 15 2 2 4-4"/></svg> -->

                            <svg  v-else  xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check-icon lucide-file-check"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="m9 15 2 2 4-4"/></svg>

                            <!-- <svg v-else class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2..." clip-rule="evenodd"/>
                            </svg> -->

                            

                            <p class="mt-3 text-center font-medium text-gray-800 line-clamp-2">
                                {{ item.title }}
                            </p>

                            <p class="text-xs text-gray-500">Status: {{ item.status }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 flex flex-col gap-2">

                            <button

                                v-if="(item.status === 'pending' && item.type === 'file') && roles.includes('admin_diklat')"
                                @click="openVerifyModal(item)"
                                class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Verifikasi
                            </button>

                            <button
                            
                                v-if="(item.status === 'pending' && item.type === 'file') && roles.includes('admin_diklat')"
                                @click="openRejectModal(item)"
                                class="text-xs px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Tolak
                            </button>

                            <a
                                v-if="item.type === 'file' && item.file_path"
                                :href="`/storage/${item.file_path}`"
                                target="_blank"
                                class="text-xs px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Preview
                            </a>

                            <button
                                @click="deleteMateri(item.id)"
                                class="text-xs px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                            >
                                Hapus
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔹 Modal Upload -->
        <Teleport to="body">
            <div
                v-if="tambah"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click="tambah = false"
            >
                <div
                    class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg"
                    @click.stop
                >
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">
                        Tambah Materi
                        <span v-if="currentFolder" class="text-sm font-normal text-gray-600">
                            di dalam folder: {{ currentFolder.title }}
                        </span>
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Tipe</label
                            >
                            <select
                                v-model="uploadType"
                                class="w-full rounded border border-gray-300 px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            >
                                <option value="" disabled>Pilih tipe</option>
                                <option value="folder">Folder</option>
                                <option value="file">File</option>
                            </select>
                        </div>

                        <div v-if="uploadType === 'folder'">
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Nama Folder</label
                            >
                            <input
                                v-model="uploadTitle"
                                type="text"
                                placeholder="Masukkan nama folder"
                                class="w-full rounded border border-gray-300 px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>

                        <div v-if="uploadType === 'file'" class="space-y-3">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Judul File</label
                                >
                                <input
                                    v-model="uploadTitle"
                                    type="text"
                                    placeholder="Masukkan judul file"
                                    class="w-full rounded border border-gray-300 px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-gray-700"
                                    >Pilih File</label
                                >
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    class="w-full text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            @click="tambah = false"
                            class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleUpload"
                            class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- 🔹 Modal Verifikasi -->
        <Teleport to="body">
            <div
                v-if="isVerifyModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click="isVerifyModalOpen = false"
            >
                <div
                    class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg"
                    @click.stop
                >
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">
                        Verifikasi Dokumen
                    </h2>
                    <p class="mb-2 text-gray-700">
                        Apakah Anda ingin memverifikasi dokumen:
                        <b>{{ selectedMateri?.title }}</b
                        >?
                    </p>
                    <div class="mt-5 flex justify-end gap-3">
                        <button
                            @click="isVerifyModalOpen = false"
                            class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleVerify"
                            class="rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
                        >
                            Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- 🔹 Modal Penolakan -->
        <Teleport to="body">
            <div
                v-if="isRejectModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click="isRejectModalOpen = false"
            >
                <div
                    class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg"
                    @click.stop
                >
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">
                        Tolak Dokumen
                    </h2>
                    <p class="mb-2 text-gray-700">
                        Berikan alasan penolakan untuk:
                        <b>{{ selectedMateri?.title }}</b>
                    </p>
                    <textarea
                        v-model="rejectionReason"
                        rows="3"
                        placeholder="Tuliskan alasan..."
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:ring-1 focus:ring-red-500 focus:outline-none"
                    />
                    <div class="mt-5 flex justify-end gap-3">
                        <button
                            @click="isRejectModalOpen = false"
                            class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="submitRejection"
                            class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                        >
                            Tolak
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>