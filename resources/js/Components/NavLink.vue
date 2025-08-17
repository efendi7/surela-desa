<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    theme: {
        type: String,
        default: 'light', // light or dark
    }
});

const lightClasses = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none';
const darkClasses = 'flex items-center px-3 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out focus:outline-none';

const classes = computed(() => {
    if (props.theme === 'dark') {
        return props.active
            ? darkClasses + ' bg-gray-900 text-white'
            : darkClasses + ' text-gray-300 hover:bg-gray-700 hover:text-white';
    }
    // Default theme is light
    return props.active
        ? lightClasses + ' border-indigo-400 text-gray-900 focus:border-indigo-700'
        : lightClasses + ' border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
