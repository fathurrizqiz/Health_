<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import Input from '@/components/ui/input/Input.vue';

const props = defineProps<{
    show: boolean;
    program?: any; 
}>();

const emit = defineEmits(['close']);
const form = ref({ nama_diklat: '', tahun: new Date().getFullYear().toString() });

watch(() => props.program, (newVal) => {
    if (newVal) form.value = { nama_diklat: newVal.nama_diklat, tahun: newVal.tahun };
    else form.value = { nama_diklat: '', tahun: new Date().getFullYear().toString() };
}, { immediate: true });

const submit = () => {
    const url = props.program 
        ? `/RencanaDiklat/RPT/PN/Program/Update/${props.program.id}` 
        : '/RencanaDiklat/RPT/PN/Program';
    
    router.post(url, form.value, {
        onSuccess: () => {
            toast.success(props.program ? 'Program diperbarui' : 'Program dibuat');
            emit('close');
        }
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="$emit('close')"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
            <h3 class="text-lg font-bold mb-4">{{ program ? 'Edit' : 'Tambah' }} Program</h3>
            <div class="space-y-4">
                <Input v-model="form.nama_diklat" placeholder="Nama Program" class="w-full rounded-lg border-slate-300" />
                <Input v-model="form.tahun" placeholder="Tahun" class="w-full rounded-lg border-slate-300" />
                <div class="flex justify-end gap-2 mt-4">
                    <button @click="$emit('close')" class="px-4 py-2">Batal</button>
                    <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</template>