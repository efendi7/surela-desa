<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    value: String,
    color: {
        type: String,
        default: 'gray', // Contoh: blue, green, purple, orange
    },
});

const colorClasses = computed(() => {
    return {
        'blue': 'from-blue-50 to-indigo-50 border-blue-100 text-blue-600',
        'green': 'from-green-50 to-emerald-50 border-green-100 text-green-600',
        'purple': 'from-purple-50 to-pink-50 border-purple-100 text-purple-600',
        'orange': 'from-orange-50 to-red-50 border-orange-100 text-orange-600',
    }[props.color] || 'from-gray-50 to-gray-100 border-gray-200 text-gray-600';
});

const iconBgClass = computed(() => {
    return {
        'blue': 'bg-blue-100',
        'green': 'bg-green-100',
        'purple': 'bg-purple-100',
        'orange': 'bg-orange-100',
    }[props.color] || 'bg-gray-100';
});
</script>

<template>
    <div class="bg-gradient-to-r p-6 rounded-xl border" :class="colorClasses">
        <div class="flex items-center mb-3">
            <div class="p-2 rounded-lg mr-3" :class="iconBgClass">
                <slot name="icon" />
            </div>
            <h4 class="font-semibold text-gray-900">{{ title }}</h4>
        </div>
        <p class="text-lg font-medium text-gray-800 break-words">{{ value || '-' }}</p>
    </div>
</template>