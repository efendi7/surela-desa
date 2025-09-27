<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    berita: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="berita.title" />
        <div class="container mx-auto px-4 py-8">
            <article class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
                <h1 class="text-4xl font-bold mb-4">{{ berita.title }}</h1>
                <p class="text-gray-500 mb-4">
                    Ditulis oleh {{ berita.user.name }} pada {{ new Date(berita.published_at).toLocaleString('id-ID') }}
                </p>
                <img v-if="berita.image" :src="`/storage/${berita.image}`" alt="Berita Image" class="w-full h-auto rounded-lg mb-6">
                <div class="prose max-w-none" v-html="berita.content"></div>
            </article>
        </div>
    </AuthenticatedLayout>
</template>