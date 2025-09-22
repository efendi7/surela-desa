<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'; // <-- 1. Impor komponen Link
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PengaduanFilter from './Partials/PengaduanFilter.vue';
import PengaduanTable from './Partials/PengaduanTable.vue';
import PengaduanDetailModal from './Partials/PengaduanDetailModal.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash-es';

const props = defineProps({
    pengaduan: Object,
    filters: Object,
    pageTitle: String,
});

// STATE
const showDetailModal = ref(false);
const selectedPengaduan = ref(null);

// 2. Sesuaikan filter untuk halaman riwayat (status & prioritas tidak relevan lagi)
const localFilters = ref({
    search: props.filters.search || '',
    kategori: props.filters.kategori || '',
    sort: props.filters.sort || 'desc',
});

// 3. UBAH INI: Arahkan watcher ke route 'admin.pengaduan.riwayat'
watch(localFilters, debounce((newFilters) => {
    router.get(route('admin.pengaduan.riwayat'), newFilters, { // <-- PENTING
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

// METHODS (Tidak perlu diubah)
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
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                
                <div class="mb-4 flex space-x-2">
                    <Link :href="route('admin.pengaduan.index')" 
                          class="px-4 py-2 rounded-md text-sm font-medium"
                          :class="{ 'bg-gray-900 text-white': !route().current('admin.pengaduan.riwayat'), 'bg-white text-gray-700 hover:bg-gray-50': route().current('admin.pengaduan.riwayat') }">
                        Pengaduan Aktif
                    </Link>
                    <Link :href="route('admin.pengaduan.riwayat')" 
                          class="px-4 py-2 rounded-md text-sm font-medium"
                          :class="{ 'bg-gray-900 text-white': route().current('admin.pengaduan.riwayat'), 'bg-white text-gray-700 hover:bg-gray-50': !route().current('admin.pengaduan.riwayat') }">
                        Riwayat Pengaduan
                    </Link>
                </div>
                
                <PengaduanFilter v-model="localFilters" />
                
                <PengaduanTable :pengaduan="props.pengaduan" @open-detail="openDetailModal" />

                <Pagination
                    class="mt-6"
                    :current-page="props.pengaduan.current_page"
                    :total-pages="props.pengaduan.last_page"
                    :per-page="props.pengaduan.per_page"
                    :total-items="props.pengaduan.total"
                    :links="props.pengaduan.links"
                />
            </div>
        </div>

        <PengaduanDetailModal
            :show="showDetailModal"
            :pengaduan="selectedPengaduan"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>