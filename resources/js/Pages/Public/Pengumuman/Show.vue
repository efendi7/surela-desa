<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    pengumuman: Object, // Menggunakan 'pengumuman' sebagai prop
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="pengumuman.title" />
        <div class="container mx-auto px-4 py-8">
            <article class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
                <h1 class="text-4xl font-bold mb-4">{{ pengumuman.title }}</h1>
                <p class="text-gray-500 mb-4">
                    Diumumkan oleh {{ pengumuman.user.name }} pada {{ new Date(pengumuman.published_at).toLocaleString('id-ID') }}
                </p>
                
                <!-- Tombol untuk download/lihat file PDF jika ada -->
                <div v-if="pengumuman.file_path" class="mb-6">
                    <a :href="`/storage/${pengumuman.file_path}`" target="_blank" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                        📄 Download Surat/File PDF
                    </a>
                </div>

                <img v-if="pengumuman.image" :src="`/storage/${pengumuman.image}`" alt="Pengumuman Image" class="w-full h-auto rounded-lg mb-6">
                
                <div class="prose max-w-none" v-html="pengumuman.content"></div>
            </article>
        </div>
    </AuthenticatedLayout>
</template>
