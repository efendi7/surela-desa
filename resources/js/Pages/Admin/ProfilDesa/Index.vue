<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { User, Edit3, MapPin, Phone, Mail, Hash, BookOpen, Eye, Target } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EditProfilDesaModal from './Partials/EditProfilDesaModal.vue';
import ProfileHeader from './Partials/ProfileHeader.vue';
import InfoCard from './Partials/InfoCard.vue';

// --- PROPS & STATE ---
const props = defineProps({
    profilDesa: Object,
});

const showEditModal = ref(false);
const flash = computed(() => usePage().props.flash);

// --- COMPUTED PROPERTIES ---

// Membuat string lokasi yang rapi
const locationString = computed(() => {
    return [
        props.profilDesa.nama_kecamatan ? `Kec. ${props.profilDesa.nama_kecamatan}` : '',
        props.profilDesa.nama_kabupaten ? `Kab. ${props.profilDesa.nama_kabupaten}` : '',
        props.profilDesa.nama_provinsi || '',
    ]
    .filter(Boolean) // Menghilangkan item yang kosong
    .join(' • ');
});

// Mengurai (parse) data Misi dari string JSON
// Dalam script setup Vue component
const parsedMisi = computed(() => {
    try {
        // Jika menggunakan cast di model, misi sudah berupa array
        if (Array.isArray(props.profilDesa.misi)) {
            return props.profilDesa.misi;
        }
        
        // Fallback untuk JSON string (jika tidak menggunakan cast)
        const misiData = JSON.parse(props.profilDesa.misi || '[]');
        return Array.isArray(misiData) ? misiData : [];
    } catch (e) {
        return [];
    }
});

// --- METHODS ---
const openEditModal = () => { showEditModal.value = true; };
const closeModal = () => { showEditModal.value = false; };
</script>

<template>
    <Head title="Profil Desa" />

    <AuthenticatedLayout>
        <template #header>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Profil Desa</h2>
        </template>

        <div class="pb-12">
            <!-- Header Profil -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 mb-8">
                <ProfileHeader :profil-desa="profilDesa" :location-string="locationString" />
            </div>

            <!-- Konten Utama -->
            <div class="space-y-8">
                <!-- Informasi Kontak -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <Phone class="h-6 w-6 text-blue-600" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Informasi Kontak</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <InfoCard title="Kepala Desa" :value="profilDesa.nama_kepala_desa" color="blue">
                                    <template #icon>
                                        <User class="w-5 h-5" />
                                    </template>
                                </InfoCard>
                            </div>
                            
                            <div class="group">
                                <InfoCard title="Kode Pos" :value="profilDesa.kode_pos" color="green">
                                    <template #icon>
                                        <Hash class="w-5 h-5" />
                                    </template>
                                </InfoCard>
                            </div>
                            
                            <div class="group">
                                <InfoCard title="Email" :value="profilDesa.email" color="purple">
                                    <template #icon>
                                        <Mail class="w-5 h-5" />
                                    </template>
                                </InfoCard>
                            </div>
                            
                            <div class="group">
                                <InfoCard title="Telepon" :value="profilDesa.telepon" color="orange">
                                    <template #icon>
                                        <Phone class="w-5 h-5" />
                                    </template>
                                </InfoCard>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sejarah, Visi, Misi -->
                <div class="space-y-6">
                    <!-- Sejarah Desa -->
                    <div v-if="profilDesa.sejarah" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="bg-amber-100 p-2 rounded-lg">
                                    <BookOpen class="h-6 w-6 text-amber-600" />
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">Sejarah Desa</h3>
                            </div>
                            
                           <div class="prose max-w-none">
                                    <div class="border-l-4 border-amber-200 pl-6 bg-amber-50 p-4 rounded-r-lg">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                    {{ profilDesa.sejarah }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Visi & Misi Grid -->
                    <div v-if="profilDesa.visi || profilDesa.misi" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <!-- Visi -->
                        <div v-if="profilDesa.visi" class="lg:col-span-2">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 h-full">
                                <div class="p-6 sm:p-8 h-full flex flex-col">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="bg-green-100 p-2 rounded-lg">
                                            <Eye class="h-6 w-6 text-green-600" />
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Visi</h3>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <blockquote class="border-l-4 border-green-200 pl-6 bg-green-50 p-4 rounded-r-lg h-full flex items-center">
                                            <p class="text-gray-700 italic leading-relaxed font-medium">
                                                {{ profilDesa.visi }}
                                            </p>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Misi -->
                        <div v-if="profilDesa.misi" class="lg:col-span-3">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 h-full">
                                <div class="p-6 sm:p-8">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="bg-sky-100 p-2 rounded-lg">
                                            <Target class="h-6 w-6 text-sky-600" />
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Misi</h3>
                                    </div>
                                    
                                    <div class="border-l-4 border-sky-200 pl-6 bg-sky-50 p-4 rounded-r-lg">
                                        <ol v-if="parsedMisi" class="list-decimal list-outside pl-5 space-y-3 text-gray-700 leading-relaxed">
                                            <li v-for="(item, index) in parsedMisi" :key="`misi-${index}`" class="font-medium">
                                                {{ item }}
                                            </li>
                                        </ol>
                                        <p v-else class="text-gray-700 leading-relaxed font-medium">{{ profilDesa.misi }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Edit Button -->
            <div class="fixed bottom-6 right-6 z-50">
                <button 
                    @click="openEditModal" 
                    class="group flex items-center bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-900 hover:to-black text-white font-semibold py-4 px-6 rounded-2xl shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-3xl border border-slate-700"
                >
                    <Edit3 class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform duration-300" />
                    <span class="text-sm font-bold tracking-wide">Edit Profil</span>
                </button>
            </div>
        </div>

        <EditProfilDesaModal :show="showEditModal" :profil-desa="profilDesa" @close="closeModal" />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom hover effects */
.group:hover .group-hover\:rotate-12 {
    transform: rotate(12deg);
}

/* Enhanced shadow */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>