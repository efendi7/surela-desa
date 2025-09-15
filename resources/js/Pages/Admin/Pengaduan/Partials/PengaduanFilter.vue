<script setup>
import { ref, watch } from 'vue';

const emit = defineEmits(['filter-changed']);

const searchQuery = ref('');
const filterStatus = ref('');
const sortOrder = ref('desc');

watch([searchQuery, filterStatus, sortOrder], () => {
    emit('filter-changed', {
        search: searchQuery.value,
        status: filterStatus.value,
        sort: sortOrder.value,
    });
});

const clear = () => {
    searchQuery.value = '';
    filterStatus.value = '';
    sortOrder.value = 'desc';
    emit('filter-changed', { search: '', status: '', sort: 'desc' });
};
</script>

<template>
    <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama pelapor atau judul..."
                class="w-full border-gray-300 rounded-md shadow-sm"
            />
            <select v-model="filterStatus" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Semua Status</option>
                <option value="Dikirim">Dikirim</option>
                <option value="Diterima">Diterima</option>
                <option value="Diproses">Diproses</option>
                <option value="Selesai">Selesai</option>
            </select>
            <select v-model="sortOrder" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="desc">Terbaru</option>
                <option value="asc">Terlama</option>
            </select>
        </div>
         <div class="mt-3 flex justify-end">
            <button @click="clear" class="text-sm text-indigo-600 hover:text-indigo-800">
                Bersihkan Filter
            </button>
        </div>
    </div>
</template>