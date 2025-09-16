<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const select = ref(null);
</script>

<template>
    <select
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="select"
    >
        <slot />
    </select>
</template>