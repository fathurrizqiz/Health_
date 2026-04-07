<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue3-toastify';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sertifikat',
        href: '#',
    },
    
];

interface InternalDiklat {
    id: number;
    nama_karyawan: string;
    nama_diklat: string;
    nama_pengajar: string;
    jam_diklat: number;
    post_done_at: string | null;
    pree_done_at: string | null;
    sertifikat_path: string | null;
    status: string;
}

const props = defineProps<{
    internal: InternalDiklat[];
}>();

// function downloadSertifikat(pesertaId: number | undefined) {
//     if (pesertaId == null || isNaN(pesertaId)) {
//         alert('ID peserta tidak valid');
//         return;
//     }
//     window.location.href = `/sertifikat/download/${pesertaId}`;
// }

function generateSertifikat(pesertaId: number | undefined) {
    if (pesertaId == null || isNaN(pesertaId)) {
        alert('ID peserta tidak valid');
        return;
    }
    if (!confirm('Generate sertifikat untuk peserta ini?')) return;
    router.post(`/sertifikat/generate/${pesertaId}`, {},{
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Sertifikat berhasil digenerate!');
        },
        onError: (errors) => {
            const pesanError = errors.message || 'Gagal! Karyawan belum memenuhi syarat / belum mengikuti pelatihan. konfirmasi dengan admin.';
            toast.error(pesanError);
        }
    });
}


const search = ref();
watch(search, (newVal)=>{
    router.get(route('diklat.internal.index'), { search: newVal }, {
    preserveState: true,
    replace: true,
  })
})
</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6 lg:p-8">
            
            <!-- Page Header -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Diklat Internal
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola data diklat internal dan unduh sertifikat karyawan.
                </p>
            </div>

            <!-- Main Content Card -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Toolbar (Search & Filters) -->
                <div class="flex flex-col gap-4 border-b border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                    <div class="relative w-full max-w-md">
                        <Input 
                            v-model="search" 
                            type="text"
                            class="h-10 w-full rounded-lg border border-slate-300 bg-white pl-10 pr-4 text-sm transition-shadow focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900" 
                            placeholder="Cari nama diklat atau pengajar..."
                        />
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Dummy Filter/Sort Button to make toolbar look complete -->
                    <div class="flex items-center gap-2">
                        <button class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
    
                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Pengajar</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Status Sertifikat</th>
                                <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr 
                                v-for="(item, index) in props.internal" 
                                :key="item.id"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                            >
                                <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ item.nama_diklat }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ item.nama_pengajar }}</td>
                                
                                <!-- Visual Status Column -->
                                <td class="px-6 py-4">
                                    <span v-if="item.sertifikat_path" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Belum Dibuat
                                    </span>
                                </td>
    
                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-center">
                                    <template v-if="item.id">
                                        <a
                                            v-if="item.sertifikat_path"
                                            :href="`/sertifikat/download/${item.id}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-600 transition-colors hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </a>
                                        <button
                                            v-else
                                            @click="generateSertifikat(item.id)"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Generate
                                        </button>
                                    </template>
                                    <span v-else class="text-xs italic text-slate-400">
                                        Data tidak lengkap
                                    </span>
                                </td>
                            </tr>

                            <!-- Empty State if no data -->
                            <tr v-if="!props.internal || props.internal.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                                            <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-medium text-slate-900 dark:text-slate-200">Tidak ada data</h3>
                                            <p class="mt-1 text-sm text-slate-500">Belum ada jadwal diklat internal yang tersedia saat ini.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Pagination placeholder -->
                <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Total: {{ props.internal ? props.internal.length : 0 }} diklat
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>