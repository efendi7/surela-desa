<script setup>
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';

defineProps({
    pengaduan: Object, // Di controller kita ubah jadi paginator, jadi tipenya Object
});

defineEmits(['open-detail']);

const { getStatusClass, formatDate, getPriorityClass } = usePengaduanUtils(); // Asumsi Anda menambahkan getPriorityClass di utils
</script>

<template>
    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Laporan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori & Prioritas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penanggung Jawab</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="!pengaduan.data.length">
                        <td colspan="7" class="px-6 py-16 text-center"> </td>
                    </tr>

                    <tr v-else v-for="item in pengaduan.data" :key="item.id" class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            </td>

                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ item.judul }}</div>
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-700">
                           <div>{{ item.kategori || 'Umum' }}</div>
                           <div class="font-bold" :class="getPriorityClass(item.prioritas)">{{ item.prioritas }}</div>
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-700">
                           {{ item.admin?.name || 'Belum Ditugaskan' }}
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
                            <button @click="$emit('open-detail', item)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                Lihat & Proses
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
</template>