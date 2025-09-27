<!-- UMKMDetailModal.vue -->
<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { 
    getFotoUrl, 
    getFotoPendukungUrls, 
    formatDate,
    generateWhatsAppUrl,
    generateDefaultMessage,
    formatPhoneNumber 
} from '../utils';

const props = defineProps({
    show: Boolean,
    umkm: Object,
    relatedUmkms: Array,
});

const emit = defineEmits(['close', 'view-related']);

const handleClose = () => {
    emit('close');
};

const handleContactWhatsApp = () => {
    if (!props.umkm) return;
    
    const message = generateDefaultMessage(props.umkm.nama_usaha, props.umkm.nama_pemilik);
    const whatsappUrl = generateWhatsAppUrl(props.umkm.nomor_telepon, message);
    window.open(whatsappUrl, '_blank');
};

const handleViewRelated = (relatedUmkm) => {
    emit('view-related', relatedUmkm);
};
</script>

<template>
    <Modal 
        :show="show" 
        @close="handleClose" 
        max-width="4xl"
    >
        <div v-if="umkm" class="flex flex-col max-h-[90vh] bg-white overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b bg-gradient-to-br from-slate-800 via-slate-900 to-gray-900 backdrop-blur-xl border-slate-700/50 shadow-2xl text-white">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold mb-2">{{ umkm.nama_usaha }}</h2>
                        <div class="flex items-center space-x-4 text-sm opacity-90">
                            <span class="bg-white/20 px-2 py-1 rounded text-xs">{{ umkm.kategori_usaha }}</span>
                            <span class="text-xs">Terdaftar: {{ formatDate(umkm.created_at) }}</span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="handleClose" 
                        class="text-white/80 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 rounded-md p-1"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6 space-y-6">
                    
                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Left Column - Images -->
                        <div class="bg-white rounded-lg p-4 shadow-sm space-y-4">
                            <!-- Main Image -->
                            <div class="relative">
                                <img
                                    :src="getFotoUrl(umkm.foto_produk)"
                                    :alt="`Foto produk ${umkm.nama_usaha}`"
                                    class="w-full h-48 object-cover rounded-lg"
                                    @error="$event.target.src = 'https://placehold.co/400x300/e2e8f0/64748b?text=No+Image'"
                                />
                            </div>

                            <!-- Supporting Images -->
                            <div v-if="getFotoPendukungUrls(umkm.foto_pendukung).length > 0" class="grid grid-cols-3 gap-2">
                                <img
                                    v-for="(foto, index) in getFotoPendukungUrls(umkm.foto_pendukung)"
                                    :key="`support-${index}`"
                                    :src="foto"
                                    :alt="`Foto pendukung ${index + 1}`"
                                    class="w-full h-16 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                                    @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                                />
                            </div>
                        </div>

                        <!-- Right Column - Details -->
                        <div class="space-y-4">
                            
                            <!-- Business Info -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Usaha</h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-700">Pemilik:</span>
                                            <span class="text-gray-900 ml-2">{{ umkm.nama_pemilik }}</span>
                                            <div class="text-gray-500 text-xs mt-1">NIK: {{ umkm.nik_pemilik }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-700">Alamat:</span>
                                            <span class="text-gray-900 ml-2">{{ umkm.alamat_usaha }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-700">Telepon:</span>
                                            <span class="text-gray-900 ml-2">{{ formatPhoneNumber(umkm.nomor_telepon) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-base font-semibold text-gray-900 mb-3">Deskripsi Usaha</h3>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ umkm.deskripsi }}</p>
                            </div>

                            <!-- Contact Action -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-medium text-green-800 mb-2 text-sm">Tertarik dengan usaha ini?</h4>
                                <p class="text-xs text-green-700 mb-3">Hubungi langsung pemilik melalui WhatsApp</p>
                                <button
                                    @click="handleContactWhatsApp"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    Hubungi via WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Related UMKMs -->
                    <div v-if="relatedUmkms && relatedUmkms.length > 0" class="bg-white rounded-lg p-4 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">UMKM Sejenis Lainnya</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div
                                v-for="related in relatedUmkms"
                                :key="`related-${related.id}`"
                                @click="handleViewRelated(related)"
                                class="border rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow"
                            >
                                <img
                                    :src="getFotoUrl(related.foto_produk)"
                                    :alt="related.nama_usaha"
                                    class="w-full h-16 object-cover rounded mb-2"
                                    @error="$event.target.src = 'https://placehold.co/200x150/e2e8f0/64748b?text=No+Image'"
                                />
                                <h4 class="font-medium text-xs text-gray-900 truncate">{{ related.nama_usaha }}</h4>
                                <p class="text-xs text-gray-500 truncate">{{ related.nama_pemilik }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center px-6 py-4 border-t bg-gray-50">
                <SecondaryButton @click="handleClose" class="px-6 py-2">
                    Tutup
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>