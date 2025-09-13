<script setup>
// Import ikon jika diperlukan
import { Mail, Phone } from 'lucide-vue-next';

defineProps({
    profilDesa: Object,
    locationString: String,
});

const backgroundUrl = '/images/headerDesa.jpg';
</script>

<template>
    <div
        class="relative bg-cover bg-center text-white py-16 px-6 sm:px-8 text-center sm:text-left shadow-lg overflow-hidden rounded-lg"
        :style="{ backgroundImage: `url(${backgroundUrl})` }"
    >
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8 max-w-5xl mx-auto">
            <img
                :src="profilDesa.logo ? `/storage/${profilDesa.logo}` : 'https://placehold.co/120x120/ffffff/333333?text=LOGO'"
                alt="Logo Desa"
                class="w-32 h-32 rounded-full object-cover border-4 border-white/80 shadow-2xl flex-shrink-0"
            />
            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-2 text-shadow">
                    {{ profilDesa.nama_desa }}
                </h1>
                <p class="text-gray-200 text-lg mb-3 max-w-2xl mx-auto sm:mx-0 text-shadow-sm">
                    {{ profilDesa.alamat }}
                </p>
                <p v-if="locationString" class="text-gray-300 text-sm font-medium flex items-center justify-center sm:justify-start gap-2 mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ locationString }}</span>
                </p>

                <div class="flex flex-wrap gap-x-6 gap-y-2 justify-center sm:justify-start text-gray-300 text-sm">
                    <div v-if="profilDesa.email" class="flex items-center gap-2">
                         <Mail class="w-4 h-4" />
                        <span>{{ profilDesa.email }}</span>
                    </div>
                    <div v-if="profilDesa.telepon" class="flex items-center gap-2">
                        <Phone class="w-4 h-4" />
                        <span>{{ profilDesa.telepon }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
}
.text-shadow-sm {
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}
</style>