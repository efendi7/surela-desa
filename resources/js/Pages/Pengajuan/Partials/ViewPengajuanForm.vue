<script setup>
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';
import { useStatusStyles } from '@/Composables/useStatusStyles';

const props = defineProps({
    pengajuan: Object,
});

const { formatDate } = usePengajuanUtils();
const { getStatusStyles, getStatusBadgeClass } = useStatusStyles();

const getTimelineStyles = (status) => getStatusStyles(status);
</script>

<template>
    <div v-if="pengajuan" class="p-6 space-y-6">
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="space-y-1">
                    <h3 class="text-xl font-semibold text-gray-900">{{ pengajuan.jenis_surat.nama_surat }}</h3>
                    <div class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 space-y-1 sm:space-y-0 sm:space-x-4">
                        <span>Diajukan pada {{ formatDate(pengajuan.created_at) }}</span>
                        <span v-if="pengajuan.nomor_surat" class="font-medium">No. {{ pengajuan.nomor_surat }}</span>
                    </div>

                    <!-- <div class="mt-2 text-sm text-gray-700">
                        <span class="font-medium">Admin Penanggung Jawab:</span>
                        <span>
                            {{ pengajuan.admin ? pengajuan.admin.name : 'Belum ada admin' }}
                        </span>
                    </div> -->

                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" :class="getStatusStyles(pengajuan.status).textColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusStyles(pengajuan.status).icon" />
                    </svg>
                    <span :class="getStatusBadgeClass(pengajuan.status)">
                        {{ pengajuan.status }}
                    </span>
                </div>
            </div>
        </div>

        <div v-if="pengajuan.timeline && pengajuan.timeline.length" class="bg-white rounded-lg p-6 shadow-sm">
            <h4 class="text-lg font-medium text-gray-900 mb-6">Riwayat Status</h4>
            <div class="flow-root">
                <ul class="space-y-6">
                    <li v-for="(item, index) in pengajuan.timeline" :key="index" class="relative flex items-start space-x-4">
                        <div v-if="index !== pengajuan.timeline.length - 1" class="absolute top-10 left-6 w-0.5 h-full bg-gray-200 -ml-px"></div>
                        <div class="relative flex items-center justify-center w-12 h-12 rounded-full ring-4 ring-white shadow-sm" :class="getTimelineStyles(item.status).bgColor">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="getTimelineStyles(item.status).icon" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 pb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-base font-medium text-gray-900 capitalize">{{ item.status }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ item.message }}</p>
                                    <p v-if="item.admin_name" class="text-xs text-gray-500 mt-1">oleh {{ item.admin_name }}</p>
                                </div>
                                <span class="text-sm text-gray-500 whitespace-nowrap ml-4">
                                    {{ formatDate(item.timestamp) }}
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div v-if="pengajuan.lampiran && Object.keys(pengajuan.lampiran).length > 0" class="bg-white rounded-lg p-6 shadow-sm">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Dokumen Pendukung</h4>
            <div class="grid gap-4">
                <div v-for="(file, key) in pengajuan.lampiran" :key="key" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-medium text-gray-900">{{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
                            <p class="text-sm text-gray-500">{{ file.original_name || file.name || 'Document.pdf' }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a 
                            :href="route('pengajuan.lampiran.view', { pengajuan: pengajuan.id, key: key })" 
                            target="_blank" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                            :class="{ 'opacity-50 cursor-not-allowed': !file.exists }"
                            :disabled="!file.exists"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>