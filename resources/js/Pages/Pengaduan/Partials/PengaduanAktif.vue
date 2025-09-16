<script setup>
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';

defineProps({
    pengaduanAktif: Array,
});

const emit = defineEmits(['view-detail', 'cancel']);
const { getStatusClass, getStatusIcon, formatDate } = usePengaduanUtils();
</script>

<template>
    <div class="border rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Masalah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Laporan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr v-if="!pengaduanAktif?.length">
                        <td colspan="4" class="px-6 py-16 text-center">
                            <h3 class="text-sm font-medium text-gray-900">Tidak ada pengaduan aktif</h3>
                            <p class="text-sm text-gray-500">Laporan yang sedang diproses akan muncul di sini.</p>
                        </td>
                    </tr>
                    <tr v-else v-for="laporan in pengaduanAktif" :key="laporan.id" class="hover:bg-gray-50">
                        <td class="px-4 py-4 font-medium text-gray-900 text-sm whitespace-nowrap">{{ laporan.judul }}</td>
                         <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">{{ laporan.kategori || '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">{{ formatDate(laporan.created_at) }}</td>
                        <td class="px-4 py-4">
                             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize" :class="getStatusClass(laporan.status)">
                                {{ laporan.status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right space-x-2 whitespace-nowrap">
                             <button @click="$emit('view-detail', laporan)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Detail</button>
                             <button v-if="laporan.status === 'Dikirim'" @click="$emit('cancel', laporan)" class="text-red-600 hover:text-red-900 text-sm font-medium">Batalkan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>