<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    templateOptions: Array,
    syaratOptions: Array,
});

const emit = defineEmits(['filter-changed', 'clear-filters']);

// State lokal untuk setiap input filter
const searchQuery = ref('');
const filterTemplate = ref('');
const filterSyarat = ref('');
const sortOrder = ref('asc');

// Mengawasi perubahan pada semua filter dan mengirimkan data gabungan ke induk
watch([searchQuery, filterTemplate, filterSyarat, sortOrder], () => {
    emit('filter-changed', {
        search: searchQuery.value,
        template: filterTemplate.value,
        syarat: filterSyarat.value,
        sort: sortOrder.value,
    });
});

// Fungsi untuk mereset semua filter
const clearFilters = () => {
    searchQuery.value = '';
    filterTemplate.value = '';
    filterSyarat.value = '';
    sortOrder.value = 'asc';
    emit('clear-filters'); // Beri tahu induk untuk mereset juga
};
</script>

<template>
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Input -->
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama surat atau deskripsi..."
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            
            <!-- Filter Template -->
            <select
                v-model="filterTemplate"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Semua Template</option>
                <option value="ada">Ada Template</option>
                <option value="tidak_ada">Tanpa Template</option>
                <option v-for="template in templateOptions" :key="template" :value="template">
                    {{ template }}
                </option>
            </select>
            
            <!-- Filter Syarat -->
            <select
                v-model="filterSyarat"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Semua Syarat</option>
                <option v-for="syarat in syaratOptions" :key="syarat" :value="syarat">
                    {{ syarat }}
                </option>
            </select>
            
            <!-- Sort Order -->
            <select
                v-model="sortOrder"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="asc">A-Z (Nama)</option>
                <option value="desc">Z-A (Nama)</option>
                <option value="newest">Terbaru Dibuat</option>
                <option value="oldest">Terlama Dibuat</option>
            </select>
        </div>
        
        <!-- Clear Filters Button -->
        <div class="mt-3 flex justify-end">
            <button
                @click="clearFilters"
                class="text-sm text-indigo-600 hover:text-indigo-800 underline"
            >
                Bersihkan Filter
            </button>
        </div>
    </div>
</template>