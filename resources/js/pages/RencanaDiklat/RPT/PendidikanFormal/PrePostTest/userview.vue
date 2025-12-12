<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Input from "@/components/ui/input/Input.vue";

interface Choice {
    id: number;
    text: string;
}

interface Question {
    id: number;
    pertanyaan: string;
    choices: Choice[];
    nrp:string;
}

interface Props {
    test: {
        id: number;
        type: string;
        questions: Question[];
    };
    detail_id: number;
    user_nrp: string | null;
}

const props = defineProps<Props>();

const answers = ref<{ [key: number]: number | null }>({});
const loading = ref(false);
const nrp = ref(props.user_nrp || '');

const submitTest = () => {
    console.log("Submitting test...");
    console.log("Answers:", answers.value);
    console.log("Type:", props.test.type);
    console.log("Detail ID:", props.detail_id);
    console.log("NRP:", props.user_nrp);

    loading.value = true;

    router.post(
        "/DiklatInternal/test/submit",
        {
            answers: answers.value,
            type: props.test.type,
            detail_id: props.detail_id,
            nrp: nrp.value,
        },
        {
            onStart: () => console.log("Request started..."),
            onFinish: () => {
                console.log("Request finished");
                loading.value = false;
            },
            onError: (errors) => console.error("Request errors:", errors),
            onSuccess: (page) => console.log("Server response:", page)
        }
    );
};

</script>

<template>
    <div class="max-w-3xl mx-auto p-6">

        <!-- Header -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ props.test.type === 'pree' ? 'Pre-Test' : 'Post-Test' }}
            </h2>
            <p class="text-gray-500 text-sm">Silakan jawab semua pertanyaan di bawah ini</p>
        </div>

        <!-- Questions -->
         <div class="mb-6 p-5 border border-gray-200 rounded-lg bg-white shadow-sm">
            <h2>Masukan NRP</h2>
            <Input v-model="nrp" placeholder="Masukan NRP"/>
         </div>
        <div 
            v-for="(q, index) in props.test.questions" 
            :key="q.id"
            class="mb-6 p-5 border border-gray-200 rounded-lg bg-white shadow-sm"
        >
            <h3 class="font-semibold mb-3 text-gray-800">
                {{ index + 1 }}. {{ q.pertanyaan }}
            </h3>

            <div class="space-y-2">
                <label 
                    v-for="choice in q.choices" 
                    :key="choice.id"
                    class="flex items-center space-x-3 p-2 border rounded-lg cursor-pointer hover:bg-gray-50"
                >
                    <input 
                        type="radio"
                        :name="'question_' + q.id"
                        :value="choice.id"
                        v-model="answers[q.id]"
                        class="w-4 h-4"
                    />
                    <span class="text-gray-700">{{ choice.text }}</span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-10">
            <button 
                @click="submitTest"
                :disabled="loading"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 disabled:bg-gray-400"
            >
                {{ loading ? 'Menyimpan...' : 'Kirim Jawaban' }}
            </button>
        </div>

    </div>
</template>

<style scoped>
</style>
