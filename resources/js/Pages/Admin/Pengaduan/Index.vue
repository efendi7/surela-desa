<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PengaduanFilter from './Partials/PengaduanFilter.vue';
import PengaduanTable from './Partials/PengaduanTable.vue';
import PengaduanDetailModal from './Partials/PengaduanDetailModal.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    pengaduan: Array,
});

// STATE
const showDetailModal = ref(false);
const selectedPengaduan = ref(null);
const filters = ref({ search: '', status: '', sort: 'desc' });

// COMPUTED
const filteredPengaduan = computed(() => {
    let result = [...props.pengaduan];

    if (filters.value.search) {
        const query = filters.value.search.toLowerCase();
        result = result.filter(p =>
            p.user?.name?.toLowerCase().includes(query) ||
            p.judul?.toLowerCase().includes(query)
        );
    }
    if (filters.value.status) {
        result = result.filter(p => p.status === filters.value.status);
    }

    result.sort((a, b) => {
        return filters.value.sort === 'asc'
            ? new Date(a.created_at) - new Date(b.created_at)
            : new Date(b.created_at) - new Date(a.created_at);
    });

    return result;
});

// METHODS
const openDetailModal = (item) => {
    selectedPengaduan.value = item;
    showDetailModal.value = true;
};

const closeModal = () => {
    showDetailModal.value = false;
    selectedPengaduan.value = null;
};
</script>

<template>
    <Head title="Manajemen Pengaduan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengaduan Warga</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />
                        
                        <PengaduanFilter @filter-changed="newFilters => filters = newFilters" />
                        
                        <PengaduanTable :pengaduan="filteredPengaduan" @open-detail="openDetailModal" />
                    </div>
                </div>
            </div>
        </div>

        <PengaduanDetailModal
            :show="showDetailModal"
            :pengaduan="selectedPengaduan"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>