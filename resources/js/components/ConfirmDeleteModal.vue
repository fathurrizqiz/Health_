<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded p-5 w-80 shadow-lg">
      <h2 class="text-lg font-semibold mb-3">Confirm Delete</h2>

      <p class="text-sm mb-4">
        Are you sure you want to delete this item? This action cannot be undone.
      </p>

      <div class="flex justify-end gap-2">
        <button @click="$emit('close')" class="px-3 py-1 border rounded">
          Cancel
        </button>
        <button
          @click="$emit('confirm')"
          class="px-3 py-1 bg-red-600 text-white rounded"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  show: boolean;
}>();

defineEmits<{
  (e: "close"): void;
  (e: "confirm"): void;
}>();
</script>
