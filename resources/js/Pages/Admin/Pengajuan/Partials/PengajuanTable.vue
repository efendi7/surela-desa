<script setup>
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';
import ProcessDropdown from '@/Components/ProcessDropdown.vue'

// Props
defineProps({
    pengajuan: {
        type: Array,
        required: true,
    },
    isRiwayat: {
        type: Boolean,
        default: false,
    },
});

// Emit
defineEmits([
    'open-detail',
    'trigger-upload',
    'confirm-selesai',
    'delete-file',
    'delete-riwayat',
]);

// Gunakan utils
const { getStatusClass, formatDate } = usePengajuanUtils();
</script>

<template>
    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
        <div class="table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pemohon
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis Surat
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Pengajuan
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Update Terakhir
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ isRiwayat ? 'Aksi' : 'Proses Surat' }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Empty State -->
                    <tr v-if="pengajuan.length === 0">
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pengajuan</h3>
                                <p class="text-sm text-gray-500">Belum ada data yang sesuai dengan filter yang dipilih</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Data Rows -->
                    <tr v-else v-for="item in pengajuan" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150">
                        <!-- Pemohon -->
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-xs font-medium text-gray-700">
                                        {{ item.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ item.user?.name || 'Nama tidak tersedia' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ item.user?.nik || 'NIK tidak tersedia' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Jenis Surat -->
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ item.jenis_surat?.nama_surat || '-' }}
                            </div>
                        </td>

                        <!-- Tanggal Pengajuan -->
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(item.created_at) }}
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize" 
                                  :class="getStatusClass(item.status)">
                                {{ item.status.replace('_', ' ') }}
                            </span>
                        </td>

                        <!-- Update Terakhir -->
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(item.updated_at) }}
                            </div>
                        </td>

                        <!-- Proses Surat / Aksi -->
                        <td class="px-4 py-4 text-right dropdown-column">
                            <!-- Riwayat Actions -->
                            <div v-if="isRiwayat" class="flex items-center justify-end space-x-3">
                                <!-- Detail Button -->
                                <button @click="$emit('open-detail', item)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>
                                
                                <!-- Download Button -->
                                <a v-if="item.status === 'selesai' && item.file_hasil" 
                                   :href="route('pengajuan.download', { pengajuan: item.id })" 
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Unduh
                                </a>
                                
                                <!-- Delete Button -->
                                <button @click="$emit('delete-riwayat', item)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>

                            <!-- Proses Actions - ProcessDropdown -->
                            <ProcessDropdown v-else
                                :item="item"
                                @open-detail="$emit('open-detail', $event)"
                                @trigger-upload="$emit('trigger-upload', $event)"
                                @confirm-selesai="$emit('confirm-selesai', $event)"
                                @delete-file="$emit('delete-file', $event)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.table-container {
    overflow-x: auto;
    position: relative;
}

/* Ensure dropdown column doesn't get clipped */
.dropdown-column {
    position: static;
    overflow: visible;
}

/* Alternative approach - make the last column wider for dropdown */
.table-container td:last-child {
    min-width: 200px;
}
</style>