<script setup>
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';

defineProps({
  pengajuanAktif: Array,
});

const emit = defineEmits(['view-detail', 'cancel']);

const { getStatusClass, getStatusIcon, formatDate } = usePengajuanUtils();
</script>

<template>
    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Empty State -->
                    <tr v-if="!pengajuanAktif?.length">
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada pengajuan aktif</h3>
                                <p class="text-sm text-gray-500">Pengajuan yang sedang diproses akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Data Rows -->
                    <tr v-else v-for="pengajuan in pengajuanAktif" :key="pengajuan.id" class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ pengajuan.jenis_surat?.nama_surat || '-' }}
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(pengajuan.created_at) }}
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-2">
                                    <svg class="w-4 h-4" 
                                         :class="{
                                            'text-yellow-500': pengajuan.status === 'pending',
                                            'text-blue-500 animate-spin': pengajuan.status === 'diproses',
                                            'text-orange-500': pengajuan.status === 'perlu_perbaikan'
                                         }" 
                                         fill="none" 
                                         viewBox="0 0 24 24" 
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(pengajuan.status)" />
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize" 
                                      :class="getStatusClass(pengajuan.status)">
                                    {{ pengajuan.status.replace('_', ' ') }}
                                </span>
                            </div>
                        </td>
                        
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">
                                {{ formatDate(pengajuan.updated_at) }}
                            </div>
                        </td>
                        
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <!-- Detail Button -->
                                <button @click="$emit('view-detail', pengajuan)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>
                                
                                <!-- Cancel Button (hanya untuk status pending) -->
                                <button v-if="pengajuan.status === 'pending'" 
                                        @click="$emit('cancel', pengajuan)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batalkan
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>