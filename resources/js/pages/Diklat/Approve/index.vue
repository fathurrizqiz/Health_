<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

interface DiklatItem {
    id: number;
    nama_diklat: string;
    diklat: string;
    pengajar: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    jam_diklat: number;
    penyelenggara: string;
    file_path: string | null;
    status: 'pending' | 'approved' | 'rejected';
    alasan_penolakan: string | null;
}

const props = defineProps<{
    diklat: DiklatItem[];
}>();

const showRejectModal = ref(false);
const rejectReason = ref('');
const currentDiklatId = ref<number | null>(null);

const formatDate = (dateString: string): string => {
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const approveDiklat = (id: number) => {
    if (confirm('Yakin ingin menyetujui diklat ini?')) {
        router.put(
            `/diklat/${id}/approve`,
            {},
            {
                onSuccess: () => {
                    // Refresh data setelah approval
                    location.reload();
                },
            },
        );
    }
};

const openRejectModal = (id: number) => {
    currentDiklatId.value = id;
    showRejectModal.value = true;
    rejectReason.value = '';
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    currentDiklatId.value = null;
    rejectReason.value = '';
};

const submitReject = () => {
    if (!currentDiklatId.value) return;

    router.put(
        `/diklat/${currentDiklatId.value}/reject`,
        { alasan_penolakan: rejectReason.value },
        {
            onSuccess: () => {
                closeRejectModal();
                location.reload();
            },
        },
    );
};
</script>

<template>
    <Head title="Detail Diklat" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Persetujuan Diklat
                </h1>
                <p class="mt-2 text-gray-600">
                    Daftar diklat yang menunggu persetujuan
                </p>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Nama Diklat
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Diklat
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Pengajar
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Tanggal
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Jam Diklat
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Penyelenggara
                                </th>

                                <th
                                    v-if="
                                        props.diklat.some(
                                            (item) =>
                                                item.status === 'rejected',
                                        )
                                    "
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Alasan
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Hasil
                                </th>
                                <th
                                    v-if="
                                        props.diklat.some(
                                            (item) => item.status === 'pending',
                                        )
                                    "
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="item in props.diklat" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ item.nama_diklat || '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ item.diklat || '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ item.pengajar }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ formatDate(item.tanggal_mulai) }} -
                                        {{ formatDate(item.tanggal_selesai) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2 text-xs leading-5 font-semibold text-blue-800"
                                    >
                                        {{ item.jam_diklat }} jam
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                                >
                                    {{ item.penyelenggara }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                                >
                                    {{ item.alasan_penolakan }}
                                </td>
                                <!-- Kolom aksi kondisional -->
                                <td
                                    v-if="item.status === 'pending'"
                                    class="px-6 py-4 text-sm font-medium whitespace-nowrap"
                                >
                                    <div class="flex space-x-2">
                                        <button
                                            @click="approveDiklat(item.id)"
                                            class="font-medium text-green-600 hover:text-green-900"
                                        >
                                            Setujui
                                        </button>
                                        <button
                                            @click="openRejectModal(item.id)"
                                            class="font-medium text-red-600 hover:text-red-900"
                                        >
                                            Tolak
                                        </button>
                                    </div>
                                </td>
                                <!-- Tampilkan status jika bukan pending -->
                                <td
                                    v-else
                                    class="px-6 py-4 text-sm font-medium whitespace-nowrap"
                                >
                                    <span
                                        :class="{
                                            'text-green-600':
                                                item.status === 'approved',
                                            'text-red-600':
                                                item.status === 'rejected',
                                            'text-gray-500':
                                                item.status !== 'approved' &&
                                                item.status !== 'rejected',
                                        }"
                                        class="font-medium"
                                    >
                                        {{
                                            item.status === 'approved'
                                                ? 'Disetujui'
                                                : 'Ditolak'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reject Modal -->
            <div
                v-if="showRejectModal"
                class="bg-opacity-50 fixed inset-0 z-50 h-full w-full overflow-y-auto backdrop-blur-md"
            >
                <div
                    class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg"
                >
                    <div class="mt-3">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            Alasan Penolakan
                        </h3>
                        <form @submit.prevent="submitReject">
                            <textarea
                                v-model="rejectReason"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                rows="4"
                                placeholder="Masukkan alasan penolakan..."
                                required
                            ></textarea>
                            <div class="mt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="closeRejectModal"
                                    class="rounded-md px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                                >
                                    Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
