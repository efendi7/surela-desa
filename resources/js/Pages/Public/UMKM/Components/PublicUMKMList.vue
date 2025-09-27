<script setup>
import {
    formatDate,
    getFotoUrl,
    getFotoPendukungUrls,
} from '../utils';

const props = defineProps({
    umkms: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['view-details']);
</script>

<template>
    <div v-if="umkms.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
            v-for="umkm in umkms"
            :key="umkm.id"
            class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow cursor-pointer group"
            @click="$emit('view-details', umkm)"
        >
            <img
                class="w-full h-48 object-cover rounded-t-lg group-hover:opacity-90 transition-opacity"
                :src="getFotoUrl(umkm.foto_produk)"
                :alt="`Foto produk ${umkm.nama_usaha}`"
                @error="
                    $event.target.src = 'https://placehold.co/300x200/e2e8f0/64748b?text=No+Image'
                "
            />
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                    {{ umkm.nama_usaha }}
                </h3>
                <p class="text-sm text-indigo-600 mt-1 font-medium">{{ umkm.kategori_usaha }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium">Pemilik:</span> {{ umkm.nama_pemilik }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ umkm.alamat_usaha.length > 50 ? umkm.alamat_usaha.substring(0, 50) + '...' : umkm.alamat_usaha }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ umkm.nomor_telepon }}
                </p>
                
                <!-- Preview Deskripsi -->
                <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                    {{ umkm.deskripsi.length > 100 ? umkm.deskripsi.substring(0, 100) + '...' : umkm.deskripsi }}
                </p>

                <!-- Foto Pendukung Preview -->
                <div v-if="getFotoPendukungUrls(umkm.foto_pendukung).length > 0" class="mt-3 flex space-x-1">
                    <img
                        v-for="(foto, index) in getFotoPendukungUrls(umkm.foto_pendukung).slice(0, 3)"
                        :key="foto"
                        :src="foto"
                        class="w-12 h-12 object-cover rounded border opacity-80 hover:opacity-100 transition-opacity"
                        @error="
                            $event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'
                        "
                    />
                    <div 
                        v-if="getFotoPendukungUrls(umkm.foto_pendukung).length > 3"
                        class="w-12 h-12 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500 font-medium"
                    >
                        +{{ getFotoPendukungUrls(umkm.foto_pendukung).length - 3 }}
                    </div>
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400">{{ formatDate(umkm.created_at) }}</span>
                    <button
                        @click.stop="$emit('view-details', umkm)"
                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors"
                    >
                        Lihat Detail
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div v-else class="text-center py-16">
        <svg
            class="mx-auto h-16 w-16 text-gray-300"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
            />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-700">Tidak Ada UMKM Ditemukan</h3>
        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
            Maaf, tidak ada UMKM yang sesuai dengan pencarian Anda. Coba ubah filter atau kata kunci pencarian.
        </p>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>