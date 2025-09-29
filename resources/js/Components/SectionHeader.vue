<script setup>
defineProps({
    title: String,
    description: String,
});
</script>

<template>
    <div class="flex items-center space-x-3 mb-6">
        <div class="flex-shrink-0">
            <slot name="icon" />
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ title }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                {{ description }}
            </p>
        </div>
    </div>
</template>
