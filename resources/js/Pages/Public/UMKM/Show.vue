<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PhoneIcon, UserIcon, MapPinIcon } from '@heroicons/vue/24/outline';


const props = defineProps({
    umkm: {
        type: Object,
        required: true,
    },
});

// Fungsi untuk mendapatkan URL foto yang bisa diakses publik
const getFotoUrl = (path) => {
    return path ? `/storage/${path.replace('public/', '')}` : 'https://placehold.co/1200x800/e2e8f0/64748b?text=Produk+Unggulan';
}
</script>

<template>
    <Head :title="umkm.nama_usaha" />

    <PublicLayout>
        <div class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                    
                    <!-- Kolom Gambar -->
                    <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden shadow-lg">
                        <img :src="getFotoUrl(umkm.foto_produk)" :alt="`Foto produk ${umkm.nama_usaha}`" class="w-full h-full object-cover">
                    </div>

                    <!-- Kolom Informasi -->
                    <div class="flex flex-col h-full">
                        <div class="mb-4">
                            <Link :href="route('umkm.public.index')" class="text-sm text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
                                &larr; Kembali ke Direktori
                            </Link>
                        </div>
                        
                        <span class="inline-block bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-1 rounded-full mb-3 self-start">
                            {{ umkm.kategori_usaha }}
                        </span>

                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                            {{ umkm.nama_usaha }}
                        </h1>

                        <div class="mt-6 space-y-6 text-gray-600">
                            <p class="text-base leading-relaxed">
                                {{ umkm.deskripsi }}
                            </p>
                            
                            <div class="border-t border-gray-200 pt-6 space-y-4">
                                <div class="flex items-center">
                                    <UserIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm text-gray-500">Pemilik Usaha</p>
                                        <p class="font-medium text-gray-800">{{ umkm.nama_pemilik }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <MapPinIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm text-gray-500">Alamat</p>
                                        <p class="font-medium text-gray-800">{{ umkm.alamat_usaha }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <PhoneIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm text-gray-500">Hubungi</p>
                                        <a :href="'https://wa.me/' + umkm.nomor_telepon.replace(/\D/g, '')" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ umkm.nomor_telepon }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
