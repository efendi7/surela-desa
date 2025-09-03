<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { User, Mail, Phone, MapPin } from 'lucide-vue-next';

defineProps({
    pageTitle: String,
    content: [String, Array],
    contentType: String,
    profilDesa: Object, // Tambahan untuk data profil desa
});

// Fungsi untuk mendapatkan inisial nama
const getInitials = (nama) => {
    if (!nama || nama === 'Belum diisi') return '?';
    return nama.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

// Fungsi untuk mendapatkan warna berdasarkan jabatan
const getJabatanColor = (jabatan) => {
    const colors = {
        'Kepala Desa': 'bg-red-500',
        'Badan Permusyawaratan Desa': 'bg-blue-500', 
        'Sekretaris Desa': 'bg-green-500',
        'Kaur Pemerintahan': 'bg-purple-500',
        'Kaur Pembangunan': 'bg-orange-500',
        'Kaur Pemberdayaan Masyarakat': 'bg-teal-500',
        'Kaur Kesejahteraan Rakyat': 'bg-pink-500',
        'Kaur Umum': 'bg-indigo-500',
        'Kaur Keuangan': 'bg-yellow-500',
    };
    
    return colors[jabatan] || 'bg-gray-500';
};
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <User class="h-6 w-6 text-gray-600" />
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Info Desa -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-lg mb-8 text-white overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div v-if="profilDesa?.logo" class="w-20 h-20 bg-white rounded-full p-2">
                                    <img :src="`/storage/${profilDesa.logo}`" 
                                         :alt="`Logo ${profilDesa.nama_desa}`"
                                         class="w-full h-full object-contain rounded-full">
                                </div>
                                <div v-else class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                                    <MapPin class="h-10 w-10 text-white" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold mb-2">
                                    {{ profilDesa?.nama_desa || 'Nama Desa' }}
                                </h1>
                                <div class="flex flex-wrap gap-4 text-red-100">
                                    <div class="flex items-center space-x-1">
                                        <MapPin class="h-4 w-4" />
                                        <span class="text-sm">
                                            {{ profilDesa?.alamat || 'Alamat belum diisi' }}
                                        </span>
                                    </div>
                                    <div v-if="profilDesa?.email" class="flex items-center space-x-1">
                                        <Mail class="h-4 w-4" />
                                        <span class="text-sm">{{ profilDesa.email }}</span>
                                    </div>
                                    <div v-if="profilDesa?.telepon" class="flex items-center space-x-1">
                                        <Phone class="h-4 w-4" />
                                        <span class="text-sm">{{ profilDesa.telepon }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 px-8 py-4">
                        <p class="text-red-100 text-center">
                            <strong>Struktur Organisasi Pemerintah Desa</strong>
                        </p>
                    </div>
                </div>

                <!-- Konten berdasarkan tipe -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Jika konten adalah HTML -->
                        <div
                            v-if="contentType === 'html'"
                            class="prose max-w-none"
                            v-html="content"
                        ></div>

                        <!-- Jika konten adalah struktur organisasi -->
                        <div v-if="contentType === 'struktur'">
                            <!-- Grid untuk card struktur organisasi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                <div
                                    v-for="perangkat in content"
                                    :key="perangkat.jabatan"
                                    class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105"
                                >
                                    <!-- Header Card dengan gradient -->
                                    <div class="relative h-24 bg-gradient-to-br from-red-500 to-red-600">
                                        <div class="absolute inset-0 bg-black/10"></div>
                                        <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2">
                                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg"
                                                 :class="getJabatanColor(perangkat.jabatan)">
                                                {{ getInitials(perangkat.nama) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content Card -->
                                    <div class="pt-8 pb-6 px-6 text-center">
                                        <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2">
                                            {{ perangkat.jabatan }}
                                        </h3>
                                        
                                        <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent mb-4"></div>
                                        
                                        <p class="text-gray-600 font-medium text-base">
                                            {{ perangkat.nama || 'Belum diisi' }}
                                        </p>
                                        
                                        <!-- Status badge -->
                                        <div class="mt-4">
                                            <span v-if="perangkat.nama && perangkat.nama !== 'Belum diisi'" 
                                                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                                Aktif
                                            </span>
                                            <span v-else 
                                                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></div>
                                                Belum diisi
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="!content || content.length === 0"
                                class="text-center py-12"
                            >
                                <svg class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    Struktur Organisasi Kosong
                                </h3>
                                <p class="text-gray-500">
                                    Struktur organisasi belum diisi. Silakan tambahkan data perangkat desa.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Styling untuk konten HTML */
.prose h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1rem;
}

/* Utility untuk line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>