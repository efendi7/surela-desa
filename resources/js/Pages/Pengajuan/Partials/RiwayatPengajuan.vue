<script setup>
import PengajuanTable from './PengajuanTable.vue';
import Tooltip from '@/Components/Tooltip.vue';

defineProps({
    riwayatPengajuan: Array,
});

const emit = defineEmits(['view-detail', 'delete-history']);
</script>

<template>
    <PengajuanTable
        :items="riwayatPengajuan"
        empty-title="Belum ada riwayat pengajuan"
        empty-message="Riwayat pengajuan surat Anda akan muncul di sini"
    >
        <template #empty-icon>
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </template>
        
        <template #actions="{ item }">
            <!-- Desktop Version - Text Buttons -->
            <div class="hidden md:flex space-x-2">
                <a
                    v-if="item.status === 'selesai' && item.file_hasil"
                    :href="route('pengajuan.download', { pengajuan: item.id })"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Unduh
                </a>
                
                <button
                    @click="$emit('view-detail', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Detail
                </button>
                
                <button
                    @click="$emit('delete-history', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Hapus
                </button>
            </div>

            <!-- Mobile Version - Icon Buttons -->
            <div class="md:hidden flex space-x-1">
                <!-- Download Button -->
                <Tooltip v-if="item.status === 'selesai' && item.file_hasil" text="Unduh File">
                    <a
                        :href="route('pengajuan.download', { pengajuan: item.id })"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                </Tooltip>

                <!-- Detail Button -->
                <Tooltip text="Lihat Detail">
                    <button
                        @click="$emit('view-detail', item)"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </Tooltip>

                <!-- Delete Button -->
                <Tooltip text="Hapus Riwayat">
                    <button
                        @click="$emit('delete-history', item)"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </Tooltip>
            </div>
        </template>
    </PengajuanTable>
</template>