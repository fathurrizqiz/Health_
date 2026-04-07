<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];

interface DiklatEksternall {
  id: number;
  program_id: number;
  tanggal_mulai: string;
  nama_diklat: string;
  nama_karyawan: string;
}

interface ProgramEksternal {
  id: number;
  nama_diklat: string;
  tahun: string;
  eksternal: DiklatEksternall[];
}

const props = defineProps<{
  DiklatEksternal: ProgramEksternal[];
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
    <Head title="Jadwal Eksternal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 md:p-6 lg:p-8">
            
            <!-- Page Header -->
            <div class="flex flex-col gap-2 border-b border-slate-200 pb-6 dark:border-slate-800">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Jadwal Diklat Eksternal
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Pantau daftar jadwal pelatihan eksternal (Pendidikan Non Formal) untuk karyawan beserta rincian pelaksanaannya.
                </p>
            </div>

            <!-- Table Container -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Empty State (Jika tidak ada data) -->
                <div 
                    v-if="!props.DiklatEksternal || props.DiklatEksternal.length === 0" 
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="rounded-full bg-slate-50 p-4 dark:bg-slate-800">
                        <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Belum ada jadwal eksternal</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Jadwal diklat eksternal akan muncul di sini setelah ditambahkan ke dalam sistem.</p>
                </div>

                <!-- Table Content -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Peserta</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Diklat</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tahun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template v-for="program in props.DiklatEksternal" :key="program.id">
                                <tr
                                    v-for="eksternal in program.eksternal"
                                    :key="eksternal.id"
                                    class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/25"
                                >
                                    <!-- Nama -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ eksternal.nama_karyawan?.charAt(0) || '?' }}
                                            </div>
                                            <span class="font-medium text-slate-900 dark:text-slate-200">
                                                {{ eksternal.nama_karyawan }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Diklat -->
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                        {{ program.nama_diklat }}
                                    </td>
                                    
                                    <!-- Tanggal Mulai -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ formatDate(eksternal.tanggal_mulai) }}
                                        </div>
                                    </td>

                                    <!-- Tahun -->
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ program.tahun }}
                                        </span>
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