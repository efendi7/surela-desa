<script setup>
import { ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PublicUMKMList from './Components/PublicUMKMList.vue';
import FilterBar from './Components/FilterBar.vue';
import Pagination from '@/Components/Pagination.vue';
import UMKMDetailModal from './Components/UMKMDetailModal.vue';
import UMKMHeader from './Components/UMKMHeader.vue'; // <— komponen header baru

const props = defineProps({
  umkms: Object,
  filters: Object,
  availableCategories: Array,
});

const page = usePage();
const showModal = ref(false);
const selectedUmkm = ref(null);
const relatedUmkms = ref([]);

// Filter
const handleFilterChange = (newFilters) => {
  router.get(route('public.umkm.index'), newFilters, {
    preserveState: true,
    preserveScroll: true,
  });
};

// Detail modal
const handleViewDetails = async (umkm) => {
  try {
    const response = await fetch(route('public.umkm.getDetail', umkm.id));
    const data = await response.json();

    if (response.ok) {
      selectedUmkm.value = data.umkm;
      relatedUmkms.value = data.relatedUmkms;
      showModal.value = true;

      // Add UMKM ID to URL
      const url = new URL(window.location);
      url.searchParams.set('modal', umkm.id);
      window.history.pushState({}, '', url);
    } else {
      alert('Gagal memuat detail UMKM: ' + (data.error || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error fetching UMKM details:', error);
    alert('Terjadi kesalahan saat memuat detail UMKM');
  }
};

const handleCloseModal = () => {
  showModal.value = false;
  selectedUmkm.value = null;
  relatedUmkms.value = [];

  // Remove modal parameter from URL
  const url = new URL(window.location);
  url.searchParams.delete('modal');
  window.history.replaceState({}, '', url);
};

const handleViewRelated = async (relatedUmkm) => {
  await handleViewDetails(relatedUmkm);
};

// Buka modal jika ada parameter modal di URL
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const modalId = urlParams.get('modal');
  if (modalId) {
    const umkm = props.umkms.data.find((u) => u.id == modalId);
    if (umkm) {
      handleViewDetails(umkm);
    }
  }
});


</script>

<template>
  <Head title="Direktori UMKM Desa" />

  <AuthenticatedLayout>
    <!-- Hero Section sekarang pakai komponen -->
    <UMKMHeader />

    <div class="py-8 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filter Bar -->
        <FilterBar
          :filters="filters"
          :available-categories="availableCategories"
          @filter-change="handleFilterChange"
        />

        <!-- Info Hasil -->
        <div class="mb-6">
          <p class="text-gray-600">
            <span class="font-medium">{{ umkms.total }}</span> UMKM ditemukan
            <span v-if="filters.search || filters.kategori || filters.pemilik">
              dengan filter yang diterapkan
            </span>
          </p>
        </div>

        <!-- UMKM List -->
        <PublicUMKMList
          :umkms="umkms.data"
          @view-details="handleViewDetails"
        />

        <!-- Pagination -->
        <div v-if="umkms.data.length > 0" class="mt-8">
          <Pagination
            :links="umkms.links"
            :totalItems="umkms.total"
            :totalPages="umkms.last_page"
          />
        </div>
      </div>
    </div>

    <!-- UMKM Detail Modal -->
    <UMKMDetailModal
      :show="showModal"
      :umkm="selectedUmkm"
      :related-umkms="relatedUmkms"
      @close="handleCloseModal"
      @view-related="handleViewRelated"
    />
  </AuthenticatedLayout>
</template>
