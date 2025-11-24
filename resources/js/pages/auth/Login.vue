<template>
    <img src="../../../../resources/assets/images/FrameFirst.jpg" alt="" class="absolute object-cover h-full w-full ">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="flex flex-col md:flex-row shadow-xl backdrop-blur-sm">
            <!-- Left image section -->
            <div class="relative h-60 w-full md:h-[315px] md:w-64 overflow-hidden rounded-t-xl md:rounded-l-xl md:rounded-tr-none bg-white">
                <img src="../../../assets/images/Diklat.png" alt="Login Visual" class="h-full w-full object-cover" />
                <div class="absolute top-10 md:top-20 left-1/2 md:left-[10%] transform -translate-x-1/2 md:translate-x-0 z-10 flex flex-col md:flex-row items-center gap-3 rounded px-2">
                    <img src="../../../assets/images/Hermina.png" alt="Logo" class="h-12 w-12 md:h-16 md:w-16 object-contain" />
                    <div class="text-lg md:text-xl text-white text-center font-semibold font-serif">Eichar</div>
                </div>      
            </div>
            <!-- Right form section -->
            <div class="w-full md:w-80 rounded-b-xl md:rounded-r-xl md:rounded-bl-none bg-white relative">
                <form @submit.prevent="handleLogin">
                    <div class="m-4 md:m-5">
                        <div class="flex justify-center gap-2">
                            <div class="flex justify-center text-xl">L</div>
                            <div class="flex justify-center text-xl">O</div>
                            <div class="flex justify-center text-xl">G</div>
                            <div class="flex justify-center text-xl">I</div>
                            <div class="flex justify-center text-xl">N</div>
                        </div>

                        <div class="mt-5 ml-0 md:ml-3 text-sm">
                            ID Pengguna
                            <div class="mt-2 h-8 w-full md:w-60 rounded-md border">
                                <input v-model="form.nrp" type="text" name="nrp" class="h-full w-full px-2 focus:outline-none" />
                            </div>
                            <span class="text-xs text-red-500" v-if="form.errors.nrp">{{ form.errors.nrp }}</span>
                        </div>

                        <div class="mt-5 ml-0 md:ml-3 text-sm">
                            Password
                            <div class="mt-2 h-8 w-full md:w-60 rounded-md border">
                                <input v-model="form.password" type="password" name="password" class="h-full w-full px-2 focus:outline-none" />
                            </div>
                            <span class="text-xs text-red-500" v-if="form.errors.password">{{ form.errors.password }}</span>
                        </div>

                        <div class="mt-8 md:mt-14 flex justify-end">
                            <button
                                type="submit"
                                class="flex h-7 w-20 items-center justify-center rounded-xl bg-[#00990F] text-sm text-white shadow-xl hover:bg-green-800"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Loading...' : 'Login' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';

const form = useForm({
    nrp: '',
    password: '',
});
const handleLogin = () => {
    form.post(route('login'), {
        onSuccess: () => {
            router.reload(); // ✅ hanya reload props global, tidak full reload halaman
        },
        onFinish: () => form.reset('password'),
    });
};
</script>