<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Detail Diklat',
        href: '/admins',
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

function downloadSertifikat(pesertaId: number | undefined) {
    if (pesertaId == null || isNaN(pesertaId)) {
        alert('ID peserta tidak valid');
        return;
    }
    window.location.href = `/sertifikat/download/${pesertaId}`;
}

function generateSertifikat(pesertaId: number | undefined) {
    if (pesertaId == null || isNaN(pesertaId)) {
        alert('ID peserta tidak valid');
        return;
    }
    if (!confirm('Generate sertifikat untuk peserta ini?')) return;
    router.post(`/sertifikat/generate/${pesertaId}`);
}

function formatDate(date: string | null) {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
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
        <div class="m-10">
            <h1 class="mb-4 text-xl font-bold">Diklat Internal</h1>
            <div class="m-5">
               
                <Input  v-model="search" class="w-56" placeholder="Cari Diklat dan pengajar..."/>
            </div>
    
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Diklat</th>
                        <th class="px-4 py-2">Pengajar</th>
                        <!-- <th class="px-4 py-2">Jam Diklat</th> -->
    
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in props.internal" :key="item.id">
                        <td class="border px-4 py-2">{{ index + 1 }}</td>
                        <td class="border px-4 py-2">{{ item.nama_diklat }}</td>
                        <td class="border px-4 py-2">{{ item.nama_pengajar }}</td>
                        <!-- <td class="border px-4 py-2">{{ item.jam_diklat }}</td> -->
    
                        <td class="border px-4 py-2 text-center">
                            <template v-if="item.id">
                                <a
                                    v-if="item.sertifikat_path"
                                    :href="`/sertifikat/download/${item.id}`"
                                    class="inline-block rounded bg-blue-500 px-2 py-1 text-white"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Download
                                </a>
                                <button
                                    v-else
                                    class="rounded bg-green-500 px-2 py-1 text-white"
                                    @click="generateSertifikat(item.id)"
                                >
                                    Generate Sertifikat
                                </button>
                            </template>
                            <span v-else class="text-gray-500"
                                >Data tidak lengkap</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
