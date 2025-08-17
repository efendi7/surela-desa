<template>
    <Head title="Manajemen Jenis Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jenis Surat</h2>
        </template>

        <div class="py-4 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900">
                        <div class="mb-4">
                            <PrimaryButton @click="openCreateModal">
                                <span class="hidden sm:inline">Tambah Jenis Surat</span>
                                <span class="sm:hidden">Tambah</span>
                            </PrimaryButton>
                        </div>

                        <div v-if="flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ flash.success }}</span>
                        </div>

                        <!-- Tabel Data - Responsive -->
                        <div class="overflow-x-auto">
                            <!-- Desktop Table -->
                            <table class="hidden md:table min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="w-1/4 py-2 px-4 text-left">Nama Surat</th>
                                        <th class="w-2/4 py-2 px-4 text-left">Deskripsi</th>
                                        <th class="w-1/4 py-2 px-4 text-left">Syarat</th>
                                        <th class="w-1/4 py-2 px-4 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="surat in jenisSurat" :key="surat.id" class="border-b">
                                        <td class="py-2 px-4">{{ surat.nama_surat }}</td>
                                        <td class="py-2 px-4">{{ surat.deskripsi }}</td>
                                        <td class="py-2 px-4">
                                            <ul v-if="surat.syarat && surat.syarat.length" class="list-disc list-inside text-sm">
                                                <li v-for="syaratItem in surat.syarat" :key="syaratItem">{{ syaratItem }}</li>
                                            </ul>
                                            <span v-else class="text-gray-500">-</span>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button @click="openViewModal(surat)" class="text-indigo-600 hover:text-indigo-900 mr-4">Detail</button>
                                            <button @click="openDeleteModal(surat)" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                    <tr v-if="jenisSurat.length === 0">
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">Belum ada data jenis surat.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Mobile Cards -->
                            <div class="md:hidden space-y-4">
                                <div v-for="surat in jenisSurat" :key="surat.id" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <div class="space-y-3">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ surat.nama_surat }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ surat.deskripsi }}</p>
                                        </div>
                                        
                                        <div v-if="surat.syarat && surat.syarat.length">
                                            <p class="text-sm font-medium text-gray-700">Syarat:</p>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <span v-for="syaratItem in surat.syarat" :key="syaratItem" 
                                                      class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    {{ syaratItem }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                                            <button @click="openViewModal(surat)" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                Detail
                                            </button>
                                            <button @click="openDeleteModal(surat)" 
                                                    class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="jenisSurat.length === 0" class="text-center py-8 text-gray-500">
                                    Belum ada data jenis surat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Modal -->
        <Modal :show="showModal" @close="closeModal" :max-width="'2xl'" :closeable="true">
            <!-- Modal Container dengan responsive height -->
            <div class="flex flex-col h-full max-h-[95vh] sm:max-h-[90vh]">
                <!-- Modal Header -->
                <div class="flex-shrink-0 p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-medium text-gray-900">{{ modalTitle }}</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        <span v-if="modalMode === 'create'">Isi detail surat yang akan ditambahkan.</span>
                        <span v-else-if="modalMode === 'view'">Lihat detail surat. Klik edit untuk mengubah.</span>
                        <span v-else>Ubah detail surat yang diperlukan.</span>
                    </p>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Nama Surat -->
                        <div>
                            <InputLabel for="nama_surat" value="Nama Surat" />
                            <TextInput 
                                id="nama_surat" 
                                type="text" 
                                class="mt-1 block w-full text-sm sm:text-base" 
                                v-model="form.nama_surat" 
                                required 
                                :disabled="isFormDisabled" 
                            />
                            <InputError class="mt-2" :message="form.errors.nama_surat" />
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <InputLabel for="deskripsi" value="Deskripsi" />
                            <TextInput 
                                id="deskripsi" 
                                type="text" 
                                class="mt-1 block w-full text-sm sm:text-base" 
                                v-model="form.deskripsi" 
                                :disabled="isFormDisabled" 
                            />
                            <InputError class="mt-2" :message="form.errors.deskripsi" />
                        </div>

                        <!-- Checkbox Grid untuk Syarat -->
                        <div>
                            <InputLabel for="syarat" value="Syarat-syarat" />
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label 
                                    v-for="syarat in syaratOptions" 
                                    :key="syarat" 
                                    class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors duration-150"
                                    :class="{
                                        'bg-indigo-50 border-indigo-300': isSyaratSelected(syarat),
                                        'opacity-50 cursor-not-allowed': isFormDisabled
                                    }"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="isSyaratSelected(syarat)" 
                                        @change="toggleSyarat(syarat)" 
                                        :disabled="isFormDisabled"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-colors duration-150" 
                                    />
                                    <span class="ml-3 text-sm text-gray-900 select-none">{{ syarat }}</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.syarat" />
                        </div>

                        <!-- View Mode Additional Info -->
                        <div v-if="modalMode === 'view' && selectedSurat" class="pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 gap-4 text-sm text-gray-600">
                                <div v-if="selectedSurat.created_at">
                                    <span class="font-medium block sm:inline">Dibuat: </span>
                                    <span class="text-gray-800">{{ new Date(selectedSurat.created_at).toLocaleDateString('id-ID', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) }}</span>
                                </div>
                                <div v-if="selectedSurat.updated_at && selectedSurat.updated_at !== selectedSurat.created_at">
                                    <span class="font-medium block sm:inline">Terakhir diubah: </span>
                                    <span class="text-gray-800">{{ new Date(selectedSurat.updated_at).toLocaleDateString('id-ID', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Modal Footer -->
                <div class="flex-shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center justify-end p-4 sm:p-6 bg-gray-50 border-t gap-2 sm:gap-0 sm:space-x-3">
                    <!-- Mode Create -->
                    <template v-if="modalMode === 'create'">
                        <SecondaryButton @click="closeModal" class="order-2 sm:order-1">Batal</SecondaryButton>
                        <PrimaryButton 
                            @click="createSurat" 
                            class="order-1 sm:order-2" 
                            :class="{ 'opacity-25': form.processing }" 
                            :disabled="form.processing"
                        >
                            Simpan
                        </PrimaryButton>
                    </template>
                    <!-- Mode View -->
                    <template v-else-if="modalMode === 'view'">
                        <SecondaryButton @click="closeModal" class="order-2 sm:order-1">Tutup</SecondaryButton>
                        <PrimaryButton @click="switchToEditMode" class="order-1 sm:order-2">Edit</PrimaryButton>
                    </template>
                    <!-- Mode Edit -->
                    <template v-else-if="modalMode === 'edit'">
                        <SecondaryButton @click="cancelEdit" class="order-2 sm:order-1">Batal</SecondaryButton>
                        <PrimaryButton 
                            @click="updateSurat" 
                            class="order-1 sm:order-2" 
                            :class="{ 'opacity-25': form.processing }" 
                            :disabled="form.processing"
                        >
                            Simpan Perubahan
                        </PrimaryButton>
                    </template>
                </div>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <DeleteModal 
            :show="showDeleteModal" 
            :surat="selectedSuratForDelete"
            @close="closeDeleteModal" 
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DeleteModal from './Modals/DeleteModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    jenisSurat: Array,
});

// --- STATE MANAGEMENT ---
const showModal = ref(false);
const modalMode = ref('create'); // Mode bisa: 'create', 'view', 'edit'
const selectedSurat = ref(null); // Menyimpan data asli surat yang dipilih

// Delete Modal State
const showDeleteModal = ref(false);
const selectedSuratForDelete = ref(null);

const form = useForm({
    id: null,
    nama_surat: '',
    deskripsi: '',
    syarat: [],
});

const syaratOptions = [
    'KTP', 'Kartu Keluarga', 'Akta Lahir', 'Surat RT/RW', 'Foto Usaha', 'Proposal Kegiatan'
];

// --- COMPUTED PROPERTIES ---
const flash = computed(() => usePage().props.flash);
const isFormDisabled = computed(() => modalMode.value === 'view');
const modalTitle = computed(() => {
    if (modalMode.value === 'create') return 'Tambah Jenis Surat Baru';
    if (modalMode.value === 'view') return 'Detail Jenis Surat';
    return 'Edit Jenis Surat';
});

// --- IMPROVED: Click outside handler removed (no longer needed for checkboxes) ---

// Handle body scroll lock when modal is open
const handleModalScroll = () => {
    if (showModal.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

// --- MODAL & FORM FUNCTIONS ---
const openCreateModal = () => {
    modalMode.value = 'create';
    
    // PENTING: Reset form ke state awal/kosong secara eksplisit
    form.reset(); // Tetap panggil untuk membersihkan error & state lainnya
    form.id = null;
    form.nama_surat = '';
    form.deskripsi = '';
    form.syarat = []; // Reset array syarat menjadi kosong
    
    selectedSurat.value = null;
    showModal.value = true;
    handleModalScroll();
};

const openViewModal = (surat) => {
    modalMode.value = 'view';
    selectedSurat.value = { ...surat };
    form.defaults({ ...surat }).reset(); // Set default form & reset ke nilai tersebut
    showModal.value = true;
    handleModalScroll();
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    handleModalScroll();
};

const createSurat = () => {
    form.post(route('admin.jenis-surat.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const updateSurat = () => {
    form.put(route('admin.jenis-surat.update', selectedSurat.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const switchToEditMode = () => {
    modalMode.value = 'edit';
};

const cancelEdit = () => {
    form.reset(); // Reset form kembali ke data awal (dari selectedSurat)
    modalMode.value = 'view';
};

// --- DELETE MODAL FUNCTIONS ---
const openDeleteModal = (surat) => {
    selectedSuratForDelete.value = surat;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedSuratForDelete.value = null;
};

// --- IMPROVED: Checklist Helper Functions ---
const toggleSyarat = (syarat) => {
    const index = form.syarat.indexOf(syarat);
    if (index > -1) form.syarat.splice(index, 1);
    else form.syarat.push(syarat);
};

const isSyaratSelected = (syarat) => form.syarat?.includes(syarat);

// --- LIFECYCLE HOOKS ---
onMounted(() => {
    // No longer need click outside handler since we removed dropdown
});

onUnmounted(() => {
    // Cleanup body overflow style
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Mobile specific improvements */
@media (max-width: 640px) {
    /* Ensure modal takes full available space on mobile */
    .modal-container {
        margin: 1rem;
        max-height: calc(100vh - 2rem);
    }
    
    /* Improve touch targets on mobile */
    button {
        min-height: 44px;
    }
    
    /* Better text sizing on mobile */
    .text-xs {
        font-size: 0.75rem;
    }
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
}

/* Better responsive spacing */
@media (max-height: 600px) {
    .max-h-48 {
        max-height: 8rem;
    }
}

/* Improved checkbox grid spacing */
@media (max-width: 640px) {
    .grid-cols-1 label {
        justify-content: flex-start;
    }
}
</style>