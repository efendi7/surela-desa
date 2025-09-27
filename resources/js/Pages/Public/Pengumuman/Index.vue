<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; 

defineProps({
    pengumumans: Object, // Menggunakan 'pengumumans' sebagai prop
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pengumuman Desa" />
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Pengumuman Terbaru</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Looping data pengumuman -->
                <div v-for="pengumuman in pengumumans.data" :key="pengumuman.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img :src="pengumuman.image ? `/storage/${pengumuman.image}` : 'https://via.placeholder.com/400x250'" alt="Pengumuman Image" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="font-bold text-xl mb-2">{{ pengumuman.title }}</h2>
                        <p class="text-gray-500 text-sm mb-4">{{ new Date(pengumuman.published_at).toLocaleDateString('id-ID') }}</p>
                        <!-- Link ke halaman detail pengumuman -->
                        <Link :href="route('public.pengumuman.show', pengumuman.slug)" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            Lihat Detail &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
