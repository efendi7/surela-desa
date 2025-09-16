<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PengaduanFilter from './Partials/PengaduanFilter.vue';
import PengaduanTable from './Partials/PengaduanTable.vue';
import PengaduanDetailModal from './Partials/PengaduanDetailModal.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue'; // 1. Impor komponen Paginasi
import { debounce } from 'lodash-es'; // 2. Impor debounce untuk performa filter

const props = defineProps({
    pengaduan: Object, // 3. Props diubah menjadi Object karena menerima Paginator
    filters: Object,   // 4. Props untuk menerima state filter dari controller
    pageTitle: String,
});

// 5. HAPUS: computed property 'filteredPengaduan' sudah tidak diperlukan lagi

// STATE
const showDetailModal = ref(false);
const selectedPengaduan = ref(null);

// 6. Buat ref reaktif untuk filter, diinisialisasi dengan nilai dari props
const localFilters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    kategori: props.filters.kategori || '',
    prioritas: props.filters.prioritas || '',
    sort: props.filters.sort || 'desc',
});

// 7. TAMBAH: Watcher untuk mengirim request filter ke backend saat nilai filter berubah
watch(localFilters, debounce((newFilters) => {
    router.get(route('admin.pengaduan.index'), newFilters, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true }); // debounce 300ms agar tidak mengirim request setiap kali user mengetik

// METHODS
const openDetailModal = (item) => {
    selectedPengaduan.value = item;
    showDetailModal.value = true;
};

const closeModal = () => {
    showDetailModal.value = false;
    selectedPengaduan.value = null; // selectedPengaduan sudah di-reset di sini
};
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                
                <PengaduanFilter v-model="localFilters" />
                
                <PengaduanTable :pengaduan="props.pengaduan" @open-detail="openDetailModal" />

                <Pagination class="mt-6" :links="props.pengaduan.links" />
            </div>
        </div>

        <PengaduanDetailModal
            :show="showDetailModal"
            :pengaduan="selectedPengaduan"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>