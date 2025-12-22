<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];
interface HlcDetail {
    id: number
    program_id: number
    nama_program: string
    nama_diklat: string
    tanggal_mulai: string
    tahun: number
}

interface ProgramHlc {
    id: number
    program_id: number
    hlc: HlcDetail[]
}


const props = defineProps<{
    HLCJadwal:ProgramHlc[];
}>();
</script>
<template>
    <Head title="Evaluasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 m-5">
        <h1 class="text-2xl font-bold mb-4">Jadwal HLC</h1>

        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Program</th>
                    <th class="border px-3 py-2">Diklat</th>
                    <th class="border px-3 py-2">Tanggal Mulai</th>
                    <th class="border px-3 py-2">Tahun</th>
                </tr>
            </thead>

            <tbody>
                <template v-for="program in props.HLCJadwal" :key="program.id">
                    <tr
                        v-for="hlc in program.hlc"
                        :key="hlc.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="border px-3 py-2">
                            {{ hlc.nama_program }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ hlc.nama_diklat }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ formatDate(hlc.tanggal_mulai) }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ hlc.tahun }}
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>
