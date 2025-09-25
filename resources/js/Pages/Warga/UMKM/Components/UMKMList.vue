<script setup>
    import {
        formatDate,
        getFotoUrl,
        getFotoPendukungUrls,
        statusClass,
        formatStatus,
    } from '../utils';

    const props = defineProps({
        umkms: {
            type: Array,
            required: true,
        },
    });

    const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <div v-if="umkms.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="umkm in umkms"
            :key="umkm.id"
            class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow"
        >
            <img
                class="w-full h-48 object-cover rounded-t-lg"
                :src="getFotoUrl(umkm.foto_produk)"
                :alt="`Foto produk ${umkm.nama_usaha}`"
                @error="
                    $event.target.src = 'https://placehold.co/300x200/e2e8f0/64748b?text=No+Image'
                "
            />
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ umkm.nama_usaha }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ umkm.kategori_usaha }}</p>
                <p class="text-xs text-gray-400 mt-1">Pemilik: {{ umkm.nama_pemilik }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ formatDate(umkm.created_at) }}</p>
                <span
                    class="mt-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full border"
                    :class="statusClass(umkm.status)"
                >
                    {{ formatStatus(umkm.status) }}
                </span>
                <div
                    v-if="umkm.status === 'ditolak' && umkm.catatan_admin"
                    class="text-xs text-red-600 mt-2 p-2 bg-red-50 rounded border-l-2 border-red-200"
                >
                    <strong>Alasan penolakan:</strong><br />
                    {{ umkm.catatan_admin }}
                </div>
                <div class="mt-4 flex space-x-2">
                    <img
                        v-for="foto in getFotoPendukungUrls(umkm.foto_pendukung)"
                        :key="foto"
                        :src="foto"
                        class="w-16 h-16 object-cover rounded-md border"
                        @error="
                            $event.target.src =
                                'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'
                        "
                    />
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button
                        @click="$emit('edit', umkm)"
                        class="inline-flex items-center px-3 py-1 border border-indigo-300 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100"
                    >
                        <svg
                            class="w-3 h-3 mr-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Edit
                    </button>
                    <button
                        @click="$emit('delete', umkm)"
                        class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100"
                    >
                        <svg
                            class="w-3 h-3 mr-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="text-center py-12">
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
        <h3 class="mt-4 text-lg font-medium text-gray-700">Belum Ada UMKM Terdaftar</h3>
        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
            Mulai daftarkan usaha Anda untuk ditampilkan di direktori UMKM desa dan jangkau lebih
            banyak pelanggan.
        </p>
        <div class="mt-8">
            <button
                @click="$emit('edit', null)"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Daftar UMKM Pertama Anda
            </button>
        </div>
    </div>
</template>
