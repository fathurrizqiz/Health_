```vue id="jzk5k1"
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue3-toastify';

// =========================
// PROPS
// =========================
const props = defineProps<{
    inboxItems: any[];
}>();

// =========================
// STATE
// =========================
const isLoading = ref<number | null>(null);
const loadingAction = ref<'setuju' | 'tolak' | null>(null);

// =========================
// HELPERS
// =========================
const formatDate = (date: string) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
};

// =========================
// ACTIONS
// =========================
const handleAction = (
    id: number,
    action: 'setuju' | 'tolak'
) => {
    isLoading.value = id;
    loadingAction.value = action;

    const routeName =
        action === 'setuju'
            ? `/HLC/Home/konfirmasi/${id}`
            : `/HLC/Home/tolak/${id}`;

    router.post(
        routeName,
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    action === 'setuju'
                        ? 'Diklat berhasil dimasukkan ke jadwal'
                        : 'Undangan berhasil ditolak'
                );
            },

            onError: () => {
                toast.error('Terjadi kesalahan');
            },

            onFinish: () => {
                isLoading.value = null;
                loadingAction.value = null;
            },
        }
    );
};
</script>

<template>
    <Head title="Inbox Undangan" />

    <AppLayout>
        <div class="mx-auto max-w-5xl p-4 md:p-6">

            <!-- HEADER -->
            <div
                class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white"
                    >
                        Kotak Masuk Diklat
                    </h1>

                    <p
                        class="mt-2 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Persetujuan rekomendasi program diklat HLC.
                    </p>
                </div>

                <!-- BADGE -->
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    <span
                        class="h-2 w-2 rounded-full bg-blue-500"
                    ></span>

                    {{ inboxItems.length }} Undangan
                </div>
            </div>

            <!-- LIST -->
            <div
                v-if="inboxItems.length > 0"
                class="space-y-5"
            >
                <div
                    v-for="item in inboxItems"
                    :key="item.id"
                    class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between"
                    >

                        <!-- LEFT CONTENT -->
                        <div class="flex flex-1 gap-4">

                            <!-- ICON -->
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
                            >
                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                    />
                                </svg>
                            </div>

                            <!-- DETAIL -->
                            <div class="flex-1">

                                <!-- BADGE -->
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span
                                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                    >
                                        Rekomendasi HLC
                                    </span>

                                    <span
                                        class="text-xs text-slate-400"
                                    >
                                        Baru
                                    </span>
                                </div>

                                <!-- TITLE -->
                                <h2
                                    class="text-xl font-bold text-slate-900 dark:text-white"
                                >
                                    {{ item.nama_diklat }}
                                </h2>

                                <!-- ORGANIZER -->
                                <p
                                    class="mt-2 text-sm text-slate-600 dark:text-slate-300"
                                >
                                    Diselenggarakan oleh
                                    <span class="font-semibold">
                                        {{ item.penyelenggara }}
                                    </span>
                                </p>

                                <!-- GRID INFO -->
                                <div
                                    class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3"
                                >

                                    <!-- TANGGAL -->
                                    <div
                                        class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"
                                    >
                                        <div
                                            class="text-xs font-medium text-slate-500"
                                        >
                                            Jadwal
                                        </div>

                                        <div
                                            class="mt-1 text-sm font-semibold text-slate-800 dark:text-white"
                                        >
                                            {{ formatDate(item.tanggal_mulai) }}
                                        </div>
                                    </div>

                                    <!-- JAM -->
                                    <div
                                        class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"
                                    >
                                        <div
                                            class="text-xs font-medium text-slate-500"
                                        >
                                            Durasi
                                        </div>

                                        <div
                                            class="mt-1 text-sm font-semibold text-blue-600 dark:text-blue-400"
                                        >
                                            {{ item.jam_diklat }} Jam
                                        </div>
                                    </div>

                                    <!-- STATUS -->
                                    <div
                                        class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800"
                                    >
                                        <div
                                            class="text-xs font-medium text-slate-500"
                                        >
                                            Status
                                        </div>

                                        <div
                                            class="mt-1 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                                        >
                                            Menunggu Persetujuan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ACTION -->
                        <div
                            class="flex shrink-0 flex-row gap-3 lg:flex-col"
                        >

                            <!-- SETUJU -->
                            <button
                                @click="handleAction(item.id, 'setuju')"
                                :disabled="isLoading === item.id"
                                class="inline-flex min-w-[130px] items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span
                                    v-if="
                                        isLoading === item.id &&
                                        loadingAction === 'setuju'
                                    "
                                >
                                    Memproses...
                                </span>

                                <span v-else>
                                    Setujui
                                </span>
                            </button>

                            <!-- TOLAK -->
                            <button
                                @click="handleAction(item.id, 'tolak')"
                                :disabled="isLoading === item.id"
                                class="inline-flex min-w-[130px] items-center justify-center rounded-2xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-600 transition-all hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-red-800 dark:bg-slate-900 dark:text-red-400 dark:hover:bg-red-900/10"
                            >
                                <span
                                    v-if="
                                        isLoading === item.id &&
                                        loadingAction === 'tolak'
                                    "
                                >
                                    Memproses...
                                </span>

                                <span v-else>
                                    Tolak
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EMPTY -->
            <div
                v-else
                class="rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 py-20 text-center dark:border-slate-700 dark:bg-slate-900"
            >
                <div
                    class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-white text-3xl shadow-sm dark:bg-slate-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-inbox-icon lucide-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                </div>

                <h3
                    class="text-lg font-bold text-slate-800 dark:text-white"
                >
                    Kotak Masuk Kosong
                </h3>

                <p
                    class="mt-2 text-sm text-slate-500 dark:text-slate-400"
                >
                    Belum ada rekomendasi diklat baru untuk Anda.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
