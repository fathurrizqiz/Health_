<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Swal from 'sweetalert2';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Persetujuan',
        href: '#',
    },
];

interface DiklatItem {
    id: number;
    nama_diklat: string;
    diklat: string;
    pengajar: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    jam_diklat: number;
    penyelenggara: string;
    file_path: string | null;
    status: 'pending' | 'approved' | 'rejected';
    alasan_penolakan: string | null;
}

const props = defineProps<{
    diklat: DiklatItem[];
    filters: {
        search?: string;
    };
    status?: string | string[];
}>();

const showRejectModal = ref(false);
const rejectReason = ref('');
const currentDiklatId = ref<number | null>(null);

const formatDate = (dateString: string): string => {
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const approveDiklat = (id: number) => {
    Swal.fire({
        title: 'Apakah anda Yakin?',
        text: 'Ingin menyetujui diklat ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/diklat/${id}/approve`, {}, {
                onSuccess: () => {
                    Swal.fire('Berhasil!', 'Diklat disetujui', 'success');
                    location.reload();
                },
            });
        }
    });
};

const openRejectModal = (id: number) => {
    currentDiklatId.value = id;
    showRejectModal.value = true;
    rejectReason.value = '';
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    currentDiklatId.value = null;
    rejectReason.value = '';
};

const submitReject = () => {
    if (!currentDiklatId.value) return;

    router.put(
        `/diklat/${currentDiklatId.value}/reject`,
        { alasan_penolakan: rejectReason.value },
        {
            onSuccess: () => {
                closeRejectModal();
                location.reload();
            },
        },
    );
};

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.status || '');

// Debounce helper
function debounce(func: (...args: any[]) => void, wait: number) {
    let timeout: NodeJS.Timeout;
    return function executedFunction(...args: any[]) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

watch(
    [search, statusFilter],
    debounce(([newSearch, newStatus]) => {
        router.get(
            route('diklat.approve.index'), 
            { search: newSearch || undefined,
                status: newStatus === 'all' ? undefined : newStatus,
             }, // Remove param if empty
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300)
);
</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        Persetujuan Diklat
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Tinjau dan kelola daftar diklat yang sedang menunggu persetujuan Anda.
                    </p>
                </div>
                <div>
                    <input type="text" v-model="search" placeholder="Cari diklat..." class="w-full md:w-64 rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                </div>
                <div>
                    <select v-model="statusFilter" class="w-full md:w-auto rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="">Semua Status</option>
                        <option value="all">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            <div v-if="props.diklat.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="item in props.diklat" 
                    :key="item.id"
                    class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 transition-all hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-slate-900 line-clamp-2" :title="item.nama_diklat">
                                {{ item.nama_diklat || '-' }}
                            </h3>
                            <p class="mt-1 text-sm font-semibold text-indigo-600">
                                {{ item.diklat || '-' }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span v-if="item.status === 'approved'" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                Disetujui
                            </span>
                            <span v-else-if="item.status === 'rejected'" class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                <div class="h-1.5 w-1.5 rounded-full bg-rose-500"></div>
                                Ditolak
                            </span>
                            <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                                Menunggu
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col space-y-4 p-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                {{ item.pengajar ? item.pengajar.charAt(0).toUpperCase() : '-' }}
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Pengajar</p>
                                <p class="text-sm font-medium text-slate-900">{{ item.pengajar || '-' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-3 ring-1 ring-inset ring-slate-200/50">
                            <div>
                                <p class="text-xs text-slate-500">Jadwal</p>
                                <p class="text-sm font-medium text-slate-900">{{ formatDate(item.tanggal_mulai) }}</p>
                                <p class="text-xs text-slate-600">s/d {{ formatDate(item.tanggal_selesai) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Durasi</p>
                                <p class="text-sm font-medium text-slate-900">{{ item.jam_diklat }} Jam</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Penyelenggara</p>
                            <p class="text-sm font-medium text-slate-900">{{ item.penyelenggara || '-' }}</p>
                        </div>

                        <div v-if="item.status === 'rejected' && item.alasan_penolakan" class="mt-2 rounded-lg bg-rose-50 p-3 ring-1 ring-inset ring-rose-200/60">
                            <p class="text-xs font-semibold text-rose-800">Alasan Penolakan:</p>
                            <p class="mt-1 text-sm text-rose-700">{{ item.alasan_penolakan }}</p>
                        </div>
                    </div>

                    <div v-if="item.status === 'pending'" class="mt-auto border-t border-slate-100 bg-slate-50/50 p-4">
                        <div class="flex items-center justify-end gap-3">
                            <button
                                @click="openRejectModal(item.id)"
                                class="inline-flex flex-1 items-center justify-center rounded-lg px-3 py-2 text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                            >
                                Tolak
                            </button>
                            <button
                                @click="approveDiklat(item.id)"
                                class="inline-flex flex-1 items-center justify-center rounded-lg bg-emerald-500 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shadow-sm"
                            >
                                Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl bg-white py-16 px-4 text-center shadow-sm ring-1 ring-slate-200/60">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 ring-8 ring-slate-50/50">
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900">Tidak ada data diklat</h3>
                <p class="mt-1 text-sm text-slate-500">Belum ada daftar diklat yang memerlukan persetujuan saat ini.</p>
            </div>

            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeRejectModal"></div>

                    <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all sm:my-8">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h3 class="text-lg font-bold text-slate-900">
                                Alasan Penolakan
                            </h3>
                        </div>
                        
                        <form @submit.prevent="submitReject">
                            <div class="px-6 py-5">
                                <p class="mb-3 text-sm text-slate-500">
                                    Silakan masukkan alasan mengapa diklat ini ditolak agar pendaftar dapat mengetahuinya.
                                </p>
                                <textarea
                                    v-model="rejectReason"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                    rows="4"
                                    placeholder="Masukkan detail alasan di sini..."
                                    required
                                ></textarea>
                            </div>
                            
                            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                                <button
                                    type="button"
                                    @click="closeRejectModal"
                                    class="inline-flex justify-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200/50 focus:outline-none"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                                >
                                    Konfirmasi Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
