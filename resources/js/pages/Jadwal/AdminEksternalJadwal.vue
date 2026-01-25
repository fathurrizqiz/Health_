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
    <div class="p-6 sm:p-8">
      <h1 class="text-3xl font-semibold text-gray-800 mb-6">Jadwal Diklat Eksternal</h1>

      <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nama</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Diklat</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Tanggal Mulai</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Tahun</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <template v-for="program in props.DiklatEksternal" :key="program.id">
              <tr
                v-for="eksternal in program.eksternal"
                :key="eksternal.id"
                class="transition-colors duration-150 hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ eksternal.nama_karyawan }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ program.nama_diklat }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ formatDate(eksternal.tanggal_mulai) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ program.tahun }}
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>