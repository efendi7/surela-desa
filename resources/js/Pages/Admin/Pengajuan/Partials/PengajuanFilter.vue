<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    jenisSuratOptions: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: '',
            jenisSurat: '',
            sort: 'desc',
        }),
    }
});

const emit = defineEmits(['filter-changed', 'clear-filters']);

const searchQuery = ref(props.filters.search);
const filterStatus = ref(props.filters.status);
const filterJenisSurat = ref(props.filters.jenisSurat);
const sortOrder = ref(props.filters.sort);

watch([searchQuery, filterStatus, filterJenisSurat, sortOrder], () => {
    emit('filter-changed', {
        search: searchQuery.value,
        status: filterStatus.value,
        jenisSurat: filterJenisSurat.value,
        sort: sortOrder.value,
    });
});

const clear = () => {
    searchQuery.value = '';
    filterStatus.value = '';
    filterJenisSurat.value = '';
    sortOrder.value = 'desc';
    emit('clear-filters');
};
</script>

<template>
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari pemohon/nomor surat..."
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            <select
                v-model="filterStatus"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
            <select
                v-model="filterJenisSurat"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Semua Jenis Surat</option>
                <option v-for="jenis in jenisSuratOptions" :key="jenis" :value="jenis">
                    {{ jenis }}
                </option>
            </select>
            <select
                v-model="sortOrder"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="desc">Terbaru</option>
                <option value="asc">Terlama</option>
                <option value="name_asc">Nama A-Z</option>
                <option value="name_desc">Nama Z-A</option>
            </select>
        </div>
        
        <div class="mt-3 flex justify-end">
            <button
                @click="clear"
                class="text-sm text-indigo-600 hover:text-indigo-800 underline"
            >
                Bersihkan Filter
            </button>
        </div>
    </div>
</template>