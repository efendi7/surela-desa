<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UMKMList from './Components/UMKMList.vue';
import UMKMFormModal from './Components/UMKMFormModal.vue';
import DeleteModal from './Components/DeleteModal.vue';
import StatsCard from './Components/StatsCard.vue';

// Props
const props = defineProps({
  umkms: {
    type: Object,
    required: true,
  },
});

// State
const showCreateModal = ref(false);
const showEditModal = ref(false);
const confirmingDeletion = ref(false);
const umkmToEdit = ref(null);
const umkmToDelete = ref(null);

// Form Definitions
const createForm = useForm({
  nama_usaha: '',
  alamat_usaha: '',
  deskripsi: '',
  kategori_usaha: '',
  nomor_telepon: '',
  foto_produk: null,
  foto_pendukung: [],
});

const editForm = useForm({
  nama_usaha: '',
  alamat_usaha: '',
  deskripsi: '',
  kategori_usaha: '',
  nomor_telepon: '',
  nama_pemilik: '',
  nik_pemilik: '',
  foto_produk: null,
  foto_pendukung: [],
  _method: 'PUT',
});

const deleteForm = useForm({});

// Computed Stats
const stats = computed(() => ({
  total: props.umkms.total || 0,
  disetujui: props.umkms.data?.filter(umkm => umkm.status === 'disetujui').length || 0,
  pending: props.umkms.data?.filter(umkm => umkm.status === 'pending').length || 0,
  ditolak: props.umkms.data?.filter(umkm => umkm.status === 'ditolak').length || 0,
}));

// Modal Handlers
const openCreateModal = () => {
  showCreateModal.value = true;
  createForm.reset();
  createForm.clearErrors();
};

const openEditModal = (umkm) => {
  umkmToEdit.value = umkm;
  Object.assign(editForm, {
    nama_usaha: umkm.nama_usaha || '',
    alamat_usaha: umkm.alamat_usaha || '',
    deskripsi: umkm.deskripsi || '',
    kategori_usaha: umkm.kategori_usaha || '',
    nomor_telepon: umkm.nomor_telepon || '',
    nama_pemilik: umkm.nama_pemilik || '',
    nik_pemilik: umkm.nik_pemilik || '',
    foto_produk: null,
    foto_pendukung: [],
  });
  showEditModal.value = true;
  editForm.clearErrors();
};

const confirmDeletion = (umkm) => {
  umkmToDelete.value = umkm;
  confirmingDeletion.value = true;
};

// Form Submission
const submitCreate = () => {
  createForm.post(route('warga.umkm.store'), {
    preserveScroll: true,
    onSuccess: () => showCreateModal.value = false,
  });
};

const submitEdit = () => {
  editForm.post(route('warga.umkm.update', umkmToEdit.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      umkmToEdit.value = null;
    },
  });
};

const deleteUmkm = () => {
  deleteForm.delete(route('warga.umkm.destroy', umkmToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      confirmingDeletion.value = false;
      umkmToDelete.value = null;
      router.visit(route('warga.umkm.index'), { method: 'get' });
    },
  });
};
</script>

<template>
  <Head title="UMKM Saya" />

  <AuthenticatedLayout>
    <template #header>
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">UMKM Saya</h2>
        <p class="text-sm text-gray-600 mt-1">Kelola pendaftaran usaha mikro, kecil, dan menengah Anda</p>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Action Buttons -->
        <div class="mb-6 flex flex-col sm:flex-row gap-3">
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Daftar UMKM
          </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          <StatsCard
            title="Total UMKM"
            :value="stats.total"
            icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
          />
        </div>

        <!-- UMKM List -->
        <UMKMList
          :umkms="umkms.data"
          @edit="openEditModal"
          @delete="confirmDeletion"
        />

        <!-- Pagination -->
        <div v-if="umkms.data.length > 0" class="mt-6">
          <Pagination :links="umkms.links" :totalItems="umkms.total" :totalPages="umkms.last_page" />
        </div>
      </div>
    </div>

    <!-- Modals -->
    <UMKMFormModal
      :show="showCreateModal"
      :form="createForm"
      title="Daftarkan UMKM Baru"
      submit-text="Daftarkan UMKM"
      @submit="submitCreate"
      @close="() => showCreateModal.value = false"
    />
    <UMKMFormModal
      :show="showEditModal"
      :form="editForm"
      :is-edit="true"
      :umkm="umkmToEdit"
      title="Edit UMKM"
      submit-text="Simpan Perubahan"
      @submit="submitEdit"
      @close="() => showEditModal.value = false"
    />
    <DeleteModal
      :show="confirmingDeletion"
      :umkm="umkmToDelete"
      @confirm="deleteUmkm"
      @close="() => confirmingDeletion.value = false"
    />
  </AuthenticatedLayout>
</template>

<style scoped>
img {
  @apply transition-opacity duration-300;
}

img[src*="placehold"] {
  @apply bg-gray-100 opacity-75;
}
</style>