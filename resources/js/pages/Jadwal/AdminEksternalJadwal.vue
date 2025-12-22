<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];
interface Eksternal {
    id: number
    program_id: number
    nama_diklat: string
    tahun: string
    tanggal_mulai: string
}

interface ProgramHlc {
    id: number
    program_id: number
    hlc: Eksternal[]
}


const props = defineProps<{
    HLCJadwal:ProgramHlc[];
}>();
</script>
<template>
    <Head title="Jadwal Eksternal" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 m-5">
        <h1 class="text-2xl font-bold mb-4">Jadwal Diklat Eksternal</h1>

        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    
                    <th class="border px-3 py-2">Diklat</th>
                    <th class="border px-3 py-2">Tanggal Mulai</th>
                    <th class="border px-3 py-2">Tahun</th>
                </tr>
            </thead>

            <tbody>
                <template v-for="program in props.HLCJadwal" :key="program.id">
                    <tr
                        v-for="eksternal in program.hlc"
                        :key="eksternal.id"
                        class="hover:bg-gray-50"
                    >
                        
                        <td class="border px-3 py-2">
                            {{ eksternal.nama_diklat }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ formatDate(eksternal.tanggal_mulai) }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ eksternal.tahun }}
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>
