<script setup>
import { Head, Link } from '@inertiajs/vue3';
// Assuming you have a AuthenticatedLayout or similar
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; 

defineProps({
    beritas: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Berita Desa" />
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Berita Terbaru</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="berita in beritas.data" :key="berita.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img :src="berita.image ? `/storage/${berita.image}` : 'https://via.placeholder.com/400x250'" alt="Berita Image" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="font-bold text-xl mb-2">{{ berita.title }}</h2>
                        <p class="text-gray-500 text-sm mb-4">{{ new Date(berita.published_at).toLocaleDateString('id-ID') }}</p>
                        <Link :href="route('public.berita.show', berita.slug)" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            Baca Selengkapnya &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>