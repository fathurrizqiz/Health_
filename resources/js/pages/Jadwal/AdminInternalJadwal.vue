<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];
interface Internal {
    id: number
    nrp: string
    nama_karyawan: string
    periode: {
        tanggal: string
        nama_pengajar: string
    }
}

const props = defineProps<{
    JadwalInternal: Internal[]
}>()

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    })
}

</script>
<template>
    <Head title="Evaluasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 m-5">
        <h1 class="text-2xl font-bold mb-4">Jadwal Internal</h1>

        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">NRP</th>
                    <th class="border px-3 py-2">Nama Karyawan</th>
                    <th class="border px-3 py-2">Tanggal Mulai</th>
                    <th class="border px-3 py-2">Nama Pengajar</th>
                </tr>
            </thead>

            <tbody>
                <template>
                    <tr
                        v-for="Internal in props.JadwalInternal"
                        :key="Internal.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="border px-3 py-2">
                            {{ Internal.nrp }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ Internal.nama_karyawan }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ formatDate(Internal.periode?.tanggal) }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ Internal.periode?.nama_pengajar }}
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>
