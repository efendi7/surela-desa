<script setup>
import PengaduanTable from './PengaduanTable.vue';
import Tooltip from '@/Components/Tooltip.vue';

defineProps({
    pengaduanAktif: Array,
});

const emit = defineEmits(['view-detail', 'cancel']);
</script>

<template>
    <PengaduanTable
        :items="pengaduanAktif"
        empty-title="Tidak ada pengaduan aktif"
        empty-message="Laporan yang sedang diproses akan muncul di sini"
    >
        <template #empty-icon>
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </template>

        <template #actions="{ item }">
            <!-- Desktop Version - Text Buttons -->
            <div class="hidden md:flex space-x-2">
                <button
                    @click="$emit('view-detail', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                >
                    Detail
                </button>
                             
                <button
                    v-if="item.status === 'Dikirim' || item.status === 'pending'"
                    @click="$emit('cancel', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                >
                    Batalkan
                </button>
            </div>

            <!-- Mobile Version - Icon Buttons -->
            <div class="md:hidden flex space-x-1">
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

                <!-- Cancel Button -->
                <Tooltip v-if="item.status === 'Dikirim' || item.status === 'pending'" text="Batalkan Pengaduan">
                    <button
                        @click="$emit('cancel', item)"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </Tooltip>
            </div>
        </template>
    </PengaduanTable>
</template>