<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import Input from '@/components/ui/input/Input.vue';

const props = defineProps<{
    show: boolean;
    programId: number;
    detail?: any;
    karyawan: any[];
}>();

const emit = defineEmits(['close']);

const form = ref({
    program_id: 0,
    nama_karyawan: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    jam_diklat: 0,
    penyelenggara: '',
    nrp: '',
});

const searchQuery = ref('');
const showResults = ref(false);

// PERBAIKAN 1: Gunakan watch pada props.show agar form selalu ter-reset 
// dengan program_id yang paling baru saat modal dibuka
watch(() => props.show, (isOpen) => {
    if (isOpen) {
        if (props.detail) {
            form.value = { ...props.detail };
            searchQuery.value = `${props.detail.nrp} - ${props.detail.nama_karyawan}`;
        } else {
            form.value = { 
                program_id: props.programId, 
                nama_karyawan: '', 
                tanggal_mulai: '', 
                tanggal_selesai: '', 
                jam_diklat: 0, 
                penyelenggara: '', 
                nrp: '' 
            };
            searchQuery.value = '';
        }
    }
});

const filteredEmployees = computed(() => {
    if (!searchQuery.value || form.value.nrp) return [];
    const q = searchQuery.value.toLowerCase();
    return props.karyawan.filter(k => 
        k.nama_karyawan.toLowerCase().includes(q) || k.nrp.includes(q)
    ).slice(0, 10);
});

const selectEmployee = (emp: any) => {
    form.value.nrp = emp.nrp;
    form.value.nama_karyawan = emp.nama_karyawan;
    searchQuery.value = `${emp.nrp} - ${emp.nama_karyawan}`;
    showResults.value = false;
};

const submit = () => {
    const url = props.detail 
        ? `/RencanaDiklat/RPT/PN/Detail/Update/${props.detail.id}` 
        : '/RencanaDiklat/RPT/PN/Detail';

    router.post(url, form.value, {
        onSuccess: () => {
            toast.success('Berhasil disimpan');
            emit('close');
        },
        // PERBAIKAN 2: Tangkap error validasi dari Inertia/Laravel dengan benar
        onError: (errors) => {
            toast.error('Gagal menyimpan: ' + Object.values(errors).join(', '));
        }
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="$emit('close')"></div>
        <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl p-6">
            <h3 class="text-lg font-bold mb-4">{{ detail ? 'Edit' : 'Tambah' }} Peserta Diklat</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 relative">
                    <label class="text-sm font-medium">Cari Karyawan</label>
                    <Input v-model="searchQuery" @input="form.nrp = ''; showResults = true" class="w-full rounded-lg border-slate-300" placeholder="Ketik NRP/Nama..."/>
                    
                    <div v-if="showResults && filteredEmployees.length" class="absolute z-10 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                        <div 
                            v-for="emp in filteredEmployees" 
                            :key="emp.id" 
                            @click="selectEmployee(emp)" 
                            class="p-2 hover:bg-blue-50 cursor-pointer text-sm"
                        >
                            {{ emp.nrp }} - {{ emp.nama_karyawan }}
                        </div>
                    </div>
                </div>

                <div class="col-span-1">
                    <label class="text-sm font-medium">Tgl Mulai</label>
                    <Input v-model="form.tanggal_mulai" type="date" class="w-full rounded-lg border-slate-300" />
                </div>
                <div class="col-span-1">
                    <label class="text-sm font-medium">Tgl Selesai</label>
                    <Input v-model="form.tanggal_selesai" type="date" class="w-full rounded-lg border-slate-300" />
                </div>
                <div>
                    <label class="text-sm font-medium">Jam Diklat (Per Hari)</label>
                    <Input v-model.number="form.jam_diklat" type="number" class="w-full rounded-lg border-slate-300" />
                </div>
                <div>
                    <label class="text-sm font-medium">Penyelenggara</label>
                    <Input v-model="form.penyelenggara" type="text" class="w-full rounded-lg border-slate-300" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button @click="$emit('close')" class="px-4 py-2">Batal</button>
                <button @click="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
</template>