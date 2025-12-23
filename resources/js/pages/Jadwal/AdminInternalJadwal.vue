<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];

interface Peserta {
  id: number;
  bagian: string;
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
    <div class="p-6 sm:p-8">
      <h1 class="text-3xl font-semibold text-gray-800 mb-6">Jadwal Diklat Internal</h1>

      <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nama Pengajar</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Bagian</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Tanggal Mulai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <template v-for="item in props.JadwalInternal" :key="item.id">
              <tr
                v-for="peserta in item.peserta"
                :key="peserta.id"
                class="transition-colors duration-150 hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ item.nama_pengajar }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ peserta.bagian }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ formatDate(item.tanggal) }}
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>