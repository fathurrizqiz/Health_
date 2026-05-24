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
    <Head title="Diklat Internal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 md:gap-6 md:p-6 lg:p-8">
            
            <!-- Page Header: Centered on mobile -->
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white md:text-2xl">
                    Diklat Internal
                </h1>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 md:text-sm">
                    Kelola data diklat internal dan unduh sertifikat karyawan.
                </p>
            </div>

            <!-- Main Content Card -->
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Toolbar: Stacked on mobile -->
                <div class="flex flex-col gap-4 border-b border-slate-200 p-4 md:p-6 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                    <div class="relative w-full sm:max-w-xs md:max-w-md">
                        <Input 
                            v-model="search" 
                            type="text"
                            class="h-10 w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 text-sm transition-shadow focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900" 
                            placeholder="Cari diklat..."
                        />
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button class="flex-1 sm:flex-none inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
    
                <!-- Desktop Table View (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase text-[10px]">No</th>
                                <th class="px-6 py-4 font-semibold uppercase text-[10px]">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase text-[10px]">Pengajar</th>
                                <th class="px-6 py-4 font-semibold uppercase text-[10px]">Jam Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase text-[10px]">Status Sertifikat</th>
                                <th class="px-6 py-4 text-center font-semibold uppercase text-[10px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="(item, index) in props.internal" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/25">
                                <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">{{ item.nama_diklat }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ item.nama_pengajar }}</td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ item.jam_diklat }}</td>
                                <td class="px-6 py-4">
                                    <span v-if="item.sertifikat_path" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Belum Dibuat
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a v-if="item.sertifikat_path" :href="`/sertifikat/download/${item.id}`" target="_blank" class="text-blue-600 font-bold hover:underline">Download</a>
                                    <button v-else @click="generateSertifikat(item.id)" class="text-emerald-600 font-bold hover:underline">Generate</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List (Visible on Mobile Only) -->
                <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="(item, index) in props.internal" :key="item.id" class="p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-bold text-slate-400">#{{ index + 1 }}</span>
                            <span v-if="item.sertifikat_path" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/30">
                                <span class="h-1 w-1 rounded-full bg-emerald-500"></span> TERSEDIA
                            </span>
                            <span v-else class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/30">
                                <span class="h-1 w-1 rounded-full bg-amber-500"></span> DRAFT
                            </span>
                        </div>

                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white leading-tight">{{ item.nama_diklat }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ item.nama_pengajar }}</p>
                        </div>

                        <div class="mt-1">
                            <a v-if="item.sertifikat_path" :href="`/sertifikat/download/${item.id}`" target="_blank" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 py-2.5 text-xs font-bold text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Download Sertifikat
                            </a>
                            <button v-else @click="generateSertifikat(item.id)" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 py-2.5 text-xs font-bold text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Generate Sertifikat
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!props.internal || props.internal.length === 0" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="1.5"/></svg>
                        <p class="text-sm text-slate-500">Tidak ada data ditemukan.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="text-center sm:text-left text-xs font-medium text-slate-500 dark:text-slate-400">
                        Total: {{ props.internal ? props.internal.length : 0 }} diklat
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>