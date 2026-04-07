<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Jadwal Internal', href: '/Diklat/Evaluasi' },
];

interface Peserta {
  id: number;
  bagian: string;
  nrp: string;
}

interface Internal {
  id: number;
  tanggal: string;
  nama_pengajar: string;
  peserta: Peserta[];
}

const props = defineProps<{
  JadwalInternal: Internal[];
}>();

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
}
</script>

<template>
    <Head title="Jadwal Internal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class=" space-y-6 p-4 md:p-6 lg:p-8">
            
            <!-- Page Header -->
            <div class="flex flex-col gap-2 border-b border-slate-200 pb-6 dark:border-slate-800">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Jadwal Diklat Internal
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Pantau daftar jadwal pelatihan internal, termasuk informasi pengajar dan rincian peserta.
                </p>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Empty State (Jika tidak ada data) -->
                <div 
                    v-if="!props.JadwalInternal || props.JadwalInternal.length === 0" 
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                        <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Belum ada jadwal</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Jadwal diklat internal akan muncul di sini setelah ditambahkan.</p>
                </div>

                <!-- Table Content -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Pengajar</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">NRP Peserta</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Bagian</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template v-for="item in props.JadwalInternal" :key="item.id">
                                <tr
                                    v-for="peserta in item.peserta"
                                    :key="peserta.id"
                                    class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                                >
                                    <!-- Pengajar -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ item.nama_pengajar?.charAt(0) || '?' }}
                                            </div>
                                            <span class="font-medium text-slate-900 dark:text-slate-200">
                                                {{ item.nama_pengajar }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- NRP -->
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                        <span class="font-mono text-sm">{{ peserta.nrp }}</span>
                                    </td>
                                    
                                    <!-- Bagian -->
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                        {{ peserta.bagian }}
                                    </td>
                                    
                                    <!-- Tanggal -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ formatDate(item.tanggal) }}
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>