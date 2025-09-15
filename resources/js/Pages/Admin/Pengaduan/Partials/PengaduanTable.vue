<script setup>
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';

// Props: Menerima data pengaduan dari halaman Indeks
defineProps({
    pengaduan: {
        type: Array,
        required: true,
    },
});

// Emit: Mengirim event ke halaman Indeks saat tombol aksi diklik
defineEmits(['open-detail']);

// Menggunakan utilitas untuk format tanggal dan status
const { getStatusClass, formatDate } = usePengaduanUtils();
</script>

<template>
    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelapor</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Laporan</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Laporan</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="!pengaduan.length">
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pengaduan</h3>
                                <p class="text-sm text-gray-500">Belum ada data yang sesuai dengan filter yang dipilih.</p>
                            </div>
                        </td>
                    </tr>

                    <tr v-else v-for="item in pengaduan" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center mr-3">
                                    <img v-if="item.user?.photo_url" :src="item.user.photo_url" alt="Foto Pelapor" class="w-full h-full object-cover" />
                                    <span v-else class="text-xs font-medium text-gray-700">
                                        {{ item.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ item.user?.name || 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">NIK: {{ item.user?.nik || 'N/A' }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ item.judul }}</div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">{{ formatDate(item.created_at) }}</div>
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize" :class="getStatusClass(item.status)">
                                {{ item.status }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                             <button @click="$emit('open-detail', item)" 
                                     class="text-indigo-600 hover:text-indigo-900 font-medium text-sm focus:outline-none focus:underline">
                                Lihat & Proses
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>