<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface JamDiklat {
    tahun: number;
    totalJamDiklat: number;
}

interface TargetKategori {
    kategori: string;
    totalKaryawan: number;
    targetPerOrang: number;
    totalTargetJam: number;
    totalJamDiklat: number;
    aktualJam: number; // Tambahkan ini
    persentase: number;
}

interface FollowDiklat {
    bagian: string;
    total_karyawan_ikut: number;
    total_jam_diklat: number;
}
const props = defineProps<{
    totalKaryawans: number;
    totalPerKategori: TargetKategori[];
    totalJamDiklat: JamDiklat;
    targetAll: number;
    realisasiPerBagian: FollowDiklat[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="m-5 flex justify-center text-xl font-bold">
                        Total Karyawan
                    </div>
                    <div class="flex justify-center text-4xl font-bold">
                        {{ totalKaryawans }}
                    </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="m-5 flex justify-center text-xl font-bold">
                        Total Jam Target
                    </div>
                    <div class="flex justify-center text-4xl font-bold">
                        {{ targetAll }}
                    </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="m-5 flex justify-center text-xl font-bold">
                        Jam Terlaksana {{ totalJamDiklat.tahun }}
                    </div>
                    <div class="flex justify-center text-4xl font-bold">
                        {{ totalJamDiklat.totalJamDiklat }}
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <div class="m-5 text-xl font-semibold">
                    Detail Pencapaian per Bagian {{ totalJamDiklat.tahun }}
                </div>

                <div class="m-5 overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-sidebar-border/50 text-sm text-muted-foreground"
                            >
                                <th class="pb-3 font-medium">Nama Bagian</th>
                                <th class="pb-3 font-medium">
                                    Jumlah Karyawan
                                </th>
                                <th class="pb-3 font-medium">Target</th>
                                <th class="pb-3 font-medium">Aktual</th>
                                <th class="pb-3 font-medium">Pencapaian</th>
                                <!-- <th class="pb-3 text-right font-medium">
                                    Persentase
                                </th> -->
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr
                                v-for="item in totalPerKategori"
                                :key="item.kategori"
                                class="border-b border-sidebar-border/30 transition-colors hover:bg-muted/50"
                            >
                                <td class="py-4">{{ item.kategori }}</td>
                                <td class="py-4">{{ item.totalKaryawan }}</td>
                                <td class="py-4">{{ item.totalTargetJam }}</td>
                                <td class="py-4">{{ item.aktualJam }}</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="hidden h-2 w-24 overflow-hidden rounded-full bg-gray-200 md:block"
                                        >
                                            <div
                                                class="h-full bg-blue-500"
                                                :style="{
                                                    width:
                                                        Math.min(
                                                            item.persentase,
                                                            100,
                                                        ) + '%',
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            :class="
                                                item.persentase >= 100
                                                    ? 'text-green-600'
                                                    : 'text-orange-600'
                                            "
                                        >
                                            {{ item.persentase }}%
                                        </span>
                                    </div>
                                </td>
                                <!-- <td
                                    class="py-4 text-right font-semibold text-blue-600 dark:text-blue-400"
                                >
                                    85%
                                </td> -->
                            </tr>
                            <tr
                                class="border-b border-sidebar-border/30 transition-colors hover:bg-muted/50"
                            >
                                <td class="py-4"></td>
                                <td class="py-4"></td>
                                <td class="py-4"></td>
                                <td
                                    class="py-4 text-right font-semibold text-green-600 dark:text-green-400"
                                ></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
