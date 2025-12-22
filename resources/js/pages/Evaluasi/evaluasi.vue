<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Evaluasi', href: '/Diklat/Evaluasi' },
];

interface Evaluasi {
    id: number;
    nama_diklat: string;
    evaluasi: string;
}

interface Detail {
    id: number;
    nama_diklat: string;
    evaluasi?: Evaluasi | null;
}

interface Program {
    id: number;
    nama_program: string;
    details: Detail[];
}

const props = defineProps<{
    evaluasiKaryawan: Evaluasi[];
    evaluasiInternal: Program[];
}>();
</script>

<template>
    <Head title="Evaluasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-10 p-6">
            <h1 class="text-2xl font-bold text-gray-800">Evaluasi Pelatihan</h1>

            <!-- ===================== -->
            <!-- Evaluasi Internal -->
            <!-- ===================== -->
            <section>
                <h2 class="mb-4 text-xl font-semibold text-gray-700">
                    Evaluasi Internal
                </h2>

                <div v-if="!evaluasiInternal.length" class="text-gray-500">
                    Belum ada data evaluasi internal.
                </div>

                <div class="space-y-6">
                    <div
                        v-for="program in evaluasiInternal"
                        :key="program.id"
                        class="rounded-lg border bg-white p-5 shadow-sm"
                    >
                        <h3 class="mb-4 text-lg font-semibold text-blue-700">
                            {{ program.nama_program }}
                        </h3>

                        <div
                            v-if="!program.details.length"
                            class="text-sm text-gray-500"
                        >
                            Tidak ada diklat.
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="detail in program.details"
                                :key="detail.id"
                                class="rounded-md border p-4"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ detail.nama_diklat }}
                                        </p>
                                    </div>

                                    <span
                                        v-if="detail.evaluasi"
                                        class="rounded bg-green-100 px-3 py-1 text-sm text-green-700"
                                    >
                                        Sudah Dievaluasi
                                    </span>

                                    <span
                                        v-else
                                        class="rounded bg-yellow-100 px-3 py-1 text-sm text-yellow-700"
                                    >
                                        Belum Ada Evaluasi
                                    </span>
                                </div>

                                <div
                                    v-if="detail.evaluasi"
                                    class="mt-3 rounded bg-gray-50 p-3 text-sm text-gray-700"
                                >
                                    <strong>Evaluasi:</strong>
                                    <p class="mt-1">
                                        {{ detail.evaluasi.evaluasi }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div v-if="!program.details.length" class="text-sm text-gray-500">
                        Tidak ada diklat.
                    </div> -->

                    <h1 class="font-bold text-xl">Diklat Karyawan</h1>
                    <div class="border p-2 rounded-md shadow">
                        <div class="space-y-3 m-3">
                            <div
                                v-for="(item, index) in evaluasiKaryawan"
                                :key="index"
                                class="rounded-md border p-4"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ item.nama_diklat }}
                                        </p>
                                    </div>
    
                                    <span
                                        v-if="item.evaluasi"
                                        class="rounded bg-green-100 px-3 py-1 text-sm text-green-700"
                                    >
                                        Sudah Dievaluasi
                                    </span>
    
                                    <span
                                        v-else
                                        class="rounded bg-yellow-100 px-3 py-1 text-sm text-yellow-700"
                                    >
                                        Belum Ada Evaluasi
                                    </span>
                                </div>
    
                                <div
                                    v-if="item.evaluasi"
                                    class="mt-3 rounded bg-gray-50 p-3 text-sm text-gray-700"
                                >
                                    <strong>Evaluasi:</strong>
                                    <p class="mt-1">
                                        {{ item.evaluasi }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
