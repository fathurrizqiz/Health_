<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';

interface Template {
    id: number;
    nama_template: string;
    slug: string;
    pesan: string;
}

const props = defineProps<{
    templates: Template[]
}>();

const form = useForm({
    nama_template: '',
    slug: '',
    pesan: '',
});

const submit = () => {
    form.post(route('template.store'), {
        onSuccess: () => form.reset(),
    });
};

// Auto-generate slug dari nama template
const generateSlug = () => {
    form.slug = form.nama_template.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
};
</script>

<template>
    <Head title="WhatsApp Templates" />

    <div class="p-6 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Template Pesan WhatsApp</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- DAFTAR TEMPLATE -->
            <div class="space-y-4">
                <div v-for="temp in templates" :key="temp.id" class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-gray-800">{{ temp.nama_template }}</h4>
                        <span class="text-[10px] bg-gray-100 px-2 py-1 rounded font-mono text-gray-500">{{ temp.slug }}</span>
                    </div>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-200 whitespace-pre-line font-mono">
                        {{ temp.pesan }}
                    </p>
                </div>
                <div v-if="templates.length === 0" class="text-center py-10 text-gray-400 italic">
                    Belum ada template yang dibuat.
                </div>
            </div>

            <!-- FORM TAMBAH TEMPLATE -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm sticky top-6 h-fit">
                <h3 class="font-bold text-gray-700 mb-4">Buat Template Baru</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 tracking-widest">Nama Template</label>
                        <input v-model="form.nama_template" @input="generateSlug" type="text" placeholder="Misal: Notifikasi Jadwal Baru" class="w-full border-gray-200 rounded-lg text-sm focus:ring-green-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 tracking-widest">Slug (ID Sistem)</label>
                        <input v-model="form.slug" type="text" readonly class="w-full border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 tracking-widest">Isi Pesan</label>
                        <textarea v-model="form.pesan" rows="6" class="w-full border-gray-200 rounded-lg text-sm focus:ring-green-500 font-mono" placeholder="Halo {nama}, jadwal diklat {judul} akan dilaksanakan pada {tanggal}..."></textarea>
                    </div>

                    <!-- TIPS PLACEHOLDER -->
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <p class="text-[10px] font-bold text-blue-600 uppercase mb-2">Placeholder Tersedia:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] bg-white border border-blue-200 px-2 py-1 rounded text-blue-700 font-mono">{nama}</span>
                            <span class="text-[10px] bg-white border border-blue-200 px-2 py-1 rounded text-blue-700 font-mono">{judul}</span>
                            <span class="text-[10px] bg-white border border-blue-200 px-2 py-1 rounded text-blue-700 font-mono">{tanggal}</span>
                            <span class="text-[10px] bg-white border border-blue-200 px-2 py-1 rounded text-blue-700 font-mono">{lokasi}</span>
                        </div>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-green-100">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Template' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>