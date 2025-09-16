<script setup>
// Di dalam file Filter Anda
import { ref, watch } from 'vue';

const emit = defineEmits(['filter-changed']);

const searchQuery = ref('');
const filterStatus = ref('');
const sortOrder = ref('desc');
// TAMBAHKAN INI
const filterKategori = ref('');
const filterPrioritas = ref('');

watch([searchQuery, filterStatus, sortOrder, filterKategori, filterPrioritas], () => { // Tambahkan ke watcher
    emit('filter-changed', {
        search: searchQuery.value,
        status: filterStatus.value,
        sort: sortOrder.value,
        // TAMBAHKAN INI
        kategori: filterKategori.value,
        prioritas: filterPrioritas.value,
    });
});

const clear = () => {
    searchQuery.value = '';
    filterStatus.value = '';
    sortOrder.value = 'desc';
    // TAMBAHKAN INI
    filterKategori.value = '';
    filterPrioritas.value = '';
    // emit juga dikosongkan
};
</script>

<template>
    <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input v-model="searchQuery" type="text" placeholder="Cari judul..." class="w-full border-gray-300 rounded-md shadow-sm" />
            
            <select v-model="filterStatus" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Semua Status</option>
                <option value="Dikirim">Dikirim</option>
                <option value="Diterima">Diterima</option>
                <option value="Diproses">Diproses</option>
                <option value="Selesai">Selesai</option>
            </select>
            
            <select v-model="filterKategori" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Semua Kategori</option>
                <option value="Infrastruktur">Infrastruktur</option>
                <option value="Kebersihan">Kebersihan</option>
                <option value="Keamanan">Keamanan</option>
                </select>

            <select v-model="filterPrioritas" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Semua Prioritas</option>
                <option value="Rendah">Rendah</option>
                <option value="Sedang">Sedang</option>
                <option value="Tinggi">Tinggi</option>
                <option value="Darurat">Darurat</option>
            </select>

            <select v-model="sortOrder" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="desc">Terbaru</option>
                <option value="asc">Terlama</option>
            </select>
        </div>
        </div>
</template>