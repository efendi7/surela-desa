<script setup>
    import { ref, computed } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PengajuanFilter from './Partials/PengajuanFilter.vue';
    import PengajuanTable from './Partials/PengajuanTable.vue';
    import PengajuanDetailModal from './Partials/PengajuanDetailModal.vue';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue'; // Import komponen pagination

    const props = defineProps({
        pengajuanList: Object,
    });

    // STATE MANAGEMENT
    const pengajuanData = ref([...props.pengajuanList.data]);
    const showDetailModal = ref(false);
    const selectedPengajuan = ref(null);
    
    // PAGINATION STATE
    const currentPage = ref(1);
    const perPage = 10; // Items per page

    // FILTER STATE
    const filters = ref({
        search: '',
        status: '',
        jenisSurat: '',
        sort: 'desc',
    });

    // COMPUTED PROPERTIES
    const jenisSuratOptions = computed(() => {
        const uniqueJenis = [
            ...new Set(pengajuanData.value.map((p) => p.jenis_surat?.nama_surat).filter(Boolean)),
        ];
        return uniqueJenis.sort();
    });
    
    const filteredPengajuan = computed(() => {
        let result = [...pengajuanData.value];

        if (filters.value.search) {
            const query = filters.value.search.toLowerCase();
            result = result.filter(
                (p) =>
                    p.user?.name?.toLowerCase().includes(query) ||
                    p.nomor_surat?.toLowerCase().includes(query) ||
                    p.jenis_surat?.nama_surat?.toLowerCase().includes(query)
            );
        }
        if (filters.value.status) {
            result = result.filter((p) => p.status === filters.value.status);
        }
        if (filters.value.jenisSurat) {
            result = result.filter((p) => p.jenis_surat?.nama_surat === filters.value.jenisSurat);
        }

        result.sort((a, b) => {
            switch (filters.value.sort) {
                case 'asc':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'name_asc':
                    return (a.user?.name || '').localeCompare(b.user?.name || '');
                case 'name_desc':
                    return (b.user?.name || '').localeCompare(a.user?.name || '');
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });

        return result;
    });

    // PAGINATION COMPUTED PROPERTIES
    const totalItems = computed(() => filteredPengajuan.value.length);
    const totalPages = computed(() => Math.ceil(totalItems.value / perPage));
    
    const paginatedPengajuan = computed(() => {
        const start = (currentPage.value - 1) * perPage;
        const end = start + perPage;
        return filteredPengajuan.value.slice(start, end);
    });

    // EVENT HANDLERS
    const handleFilterChange = (newFilters) => {
        filters.value = newFilters;
        // Reset to first page when filters change
        currentPage.value = 1;
    };

    const handleClearFilters = () => {
        filters.value = { search: '', status: '', jenisSurat: '', sort: 'desc' };
        currentPage.value = 1;
    };

    const handlePageChange = (page) => {
        currentPage.value = page;
    };

    const openDetailModal = (pengajuan) => {
        selectedPengajuan.value = pengajuan;
        showDetailModal.value = true;
    };

    const closeModal = () => {
        showDetailModal.value = false;
        selectedPengajuan.value = null;
    };

    // ACTIONS (LOGIC & API CALLS)
    const updateLocalData = (updatedPengajuan) => {
        const index = pengajuanData.value.findIndex((p) => p.id === updatedPengajuan.id);
        if (index !== -1) {
            pengajuanData.value[index] = { ...pengajuanData.value[index], ...updatedPengajuan };
        }
    };

    const submitPerubahan = ({ id, formData }) => {
        formData.patch(route('admin.proses.update', { pengajuanSurat: id }), {
            preserveScroll: true,
            onSuccess: () => {
                updateLocalData({ id, ...formData });
                closeModal();
            },
        });
    };

</script>

<template>
    <Head title="Riwayat Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Riwayat Pengajuan Surat
            </h2>
        </template>
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <FlashMessage />

                        <PengajuanFilter
                            :jenis-surat-options="jenisSuratOptions"
                            @filter-changed="handleFilterChange"
                            @clear-filters="handleClearFilters"
                        />

                        <PengajuanTable
                            :pengajuan="paginatedPengajuan"
                            :total-data="totalItems"
                            :is-riwayat="true"
                            @open-detail="openDetailModal"
                        />

                        <!-- Pagination Component -->
                        <Pagination
                            v-if="totalPages > 1"
                            :current-page="currentPage"
                            :total-pages="totalPages"
                            :per-page="perPage"
                            :total-items="totalItems"
                            @page-changed="handlePageChange"
                        />
                    </div>
                </div>
            </div>
        </div>

        <PengajuanDetailModal
            :show="showDetailModal"
            :pengajuan="selectedPengajuan"
            @close="closeModal"
            @submit="submitPerubahan"
        />
        
    </AuthenticatedLayout>
</template>