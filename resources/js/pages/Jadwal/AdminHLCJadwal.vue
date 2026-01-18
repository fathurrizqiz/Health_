<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'jadwal HLC', href: '#' },
];

interface HlcDetail {
  id: number;
  program_id: number;
  nama_diklat: string;
  tanggal_mulai: string;
  tahun: number;
}

interface ProgramHlc {
  id: number;
  program_id: number;
  nama_program: string;
  hlc: HlcDetail[];
}

const props = defineProps<{
  HLCJadwal: ProgramHlc[];
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
  <Head title="Evaluasi" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 sm:p-8">
      <h1 class="text-3xl font-semibold text-gray-800 mb-6">Jadwal HLC</h1>

      <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Program</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Diklat</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Tanggal Mulai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <template v-for="program in props.HLCJadwal" :key="program.id">
              <tr
                v-for="hlc in program.hlc"
                :key="hlc.id"
                class="transition-colors duration-150 hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ program.nama_program }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ hlc.nama_diklat }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ formatDate(hlc.tanggal_mulai) }}
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>