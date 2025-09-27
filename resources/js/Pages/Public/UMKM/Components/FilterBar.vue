<script setup>
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    filters: Object,
    availableCategories: Array,
});

const emit = defineEmits(['filter-change']);

const searchTerm = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.kategori || '');
const selectedOwner = ref(props.filters?.pemilik || '');

const applyFilters = () => {
    emit('filter-change', {
        search: searchTerm.value,
        kategori: selectedCategory.value,
        pemilik: selectedOwner.value,
    });
};

const clearFilters = () => {
    searchTerm.value = '';
    selectedCategory.value = '';
    selectedOwner.value = '';
    emit('filter-change', {
        search: '',
        kategori: '',
        pemilik: '',
    });
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cari UMKM</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pencarian
                </label>
                <TextInput
                    v-model="searchTerm"
                    type="text"
                    placeholder="Cari nama usaha, pemilik, atau deskripsi..."
                    class="w-full"
                    @keyup.enter="applyFilters"
                />
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori
                </label>
                <select
                    v-model="selectedCategory"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option value="">Semua Kategori</option>
                    <option 
                        v-for="kategori in availableCategories" 
                        :key="kategori" 
                        :value="kategori"
                    >
                        {{ kategori }}
                    </option>
                </select>
            </div>

            <!-- Owner Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pemilik
                </label>
                <TextInput
                    v-model="selectedOwner"
                    type="text"
                    placeholder="Nama pemilik..."
                    class="w-full"
                    @keyup.enter="applyFilters"
                />
            </div>
        </div>

        <div class="flex justify-between items-center mt-4 pt-4 border-t">
            <button
                @click="clearFilters"
                class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
            >
                Reset Filter
            </button>
            <button
                @click="applyFilters"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>
        </div>
    </div>
</template>
