<script setup>
defineProps({
    text: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="group relative inline-flex">
        <slot />

        <div
            class="absolute bottom-full left-1/2 z-20 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-3 py-1.5 text-sm font-light text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100"
        >
            <span class="absolute -bottom-1 left-1/2 -z-10 h-2 w-2 -translate-x-1/2 rotate-45 bg-gray-800"></span>
            {{ text }}
        </div>
    </div>
</template>