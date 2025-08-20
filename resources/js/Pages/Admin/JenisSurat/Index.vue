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

                        <!-- Filter dan Search Section -->
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

                        <!-- Hasil Filter Info -->
                        <div class="mb-4 text-sm text-gray-600">
                            Menampilkan {{ filteredJenisSurat.length }} dari {{ jenisSurat.length }} jenis surat
                        </div>

                        <!-- Tabel Data -->
                        <div class="overflow-x-auto">
                            <table class="hidden md:table min-w-full bg-white border rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="w-1/4 py-3 px-4 text-left font-semibold">
                                            <button @click="setSortOrder('nama')" class="flex items-center space-x-1 hover:text-indigo-600">
                                                <span>Nama Surat</span>
                                                <svg v-if="sortField === 'nama'" class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th class="w-2/4 py-3 px-4 text-left font-semibold">Deskripsi</th>
                                        <th class="w-1/4 py-3 px-4 text-left font-semibold">Template</th>
                                        <th class="w-1/4 py-3 px-4 text-left font-semibold">Syarat</th>
                                        <th class="w-1/4 py-3 px-4 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="surat in filteredJenisSurat" :key="surat.id" class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">{{ surat.nama_surat }}</td>
                                        <td class="py-3 px-4 text-gray-600">
                                            <div class="max-w-xs truncate" :title="surat.deskripsi">
                                                {{ surat.deskripsi || '-' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span v-if="surat.template_surat" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ surat.template_surat }}
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Tidak ada
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div v-if="surat.syarat && surat.syarat.length > 0" class="flex flex-wrap gap-1">
                                                <span v-for="(syarat, index) in surat.syarat.slice(0, 2)" :key="index" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    {{ syarat }}
                                                </span>
                                                <span v-if="surat.syarat.length > 2" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ surat.syarat.length - 2 }} lagi
                                                </span>
                                            </div>
                                            <span v-else class="text-gray-400 text-sm">Tidak ada syarat</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <button @click="openViewModal(surat)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    Detail
                                                </button>
                                                <span class="text-gray-300">|</span>
                                                <button @click="openDeleteModal(surat)" class="text-red-600 hover:text-red-900 font-medium">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredJenisSurat.length === 0">
                                        <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p>{{ jenisSurat.length === 0 ? 'Belum ada data jenis surat.' : 'Tidak ada data yang sesuai dengan filter.' }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Mobile Cards -->
                            <div class="md:hidden space-y-4">
                                <div v-for="surat in filteredJenisSurat" :key="surat.id" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="font-semibold text-gray-900">{{ surat.nama_surat }}</h3>
                                        <div class="flex space-x-2">
                                            <button @click="openViewModal(surat)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                Detail
                                            </button>
                                            <button @click="openDeleteModal(surat)" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div v-if="surat.deskripsi">
                                            <span class="text-gray-500">Deskripsi:</span>
                                            <p class="text-gray-700 mt-1">{{ surat.deskripsi }}</p>
                                        </div>
                                        
                                        <div>
                                            <span class="text-gray-500">Template:</span>
                                            <span v-if="surat.template_surat" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ surat.template_surat }}
                                            </span>
                                            <span v-else class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                Tidak ada
                                            </span>
                                        </div>
                                        
                                        <div v-if="surat.syarat && surat.syarat.length > 0">
                                            <span class="text-gray-500">Syarat:</span>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <span v-for="syarat in surat.syarat" :key="syarat" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    {{ syarat }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="filteredJenisSurat.length === 0" class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 text-gray-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>{{ jenisSurat.length === 0 ? 'Belum ada data jenis surat.' : 'Tidak ada data yang sesuai dengan filter.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal" :max-width="'2xl'" :closeable="true">
            <div class="flex flex-col h-full max-h-[95vh] sm:max-h-[90vh]">
                <div class="flex-shrink-0 p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-medium text-gray-900">{{ modalTitle }}</h2>
                </div>

                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Nama Surat -->
                        <div>
                            <InputLabel for="nama_surat" value="Nama Surat" />
                            <TextInput id="nama_surat" type="text" class="mt-1 block w-full" v-model="form.nama_surat" required :disabled="isFormDisabled" />
                            <InputError class="mt-2" :message="form.errors.nama_surat" />
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <InputLabel for="deskripsi" value="Deskripsi" />
                            <TextInput id="deskripsi" type="text" class="mt-1 block w-full" v-model="form.deskripsi" :disabled="isFormDisabled" />
                            <InputError class="mt-2" :message="form.errors.deskripsi" />
                        </div>

                        <!-- Dropdown Template Surat -->
                        <div>
                            <InputLabel for="template_surat" value="Template Surat" />
                            <select 
                                id="template_surat" 
                                v-model="form.template_surat" 
                                :disabled="isFormDisabled"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">-- Tidak ada template --</option>
                                <option v-for="template in templateOptions" :key="template" :value="template">
                                    {{ template }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.template_surat" />
                        </div>

                        <!-- Checkbox Syarat -->
                        <div>
                            <InputLabel for="syarat" value="Syarat-syarat" />
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label v-for="syarat in syaratOptions" :key="syarat" class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50" :class="{'bg-indigo-50 border-indigo-300': isSyaratSelected(syarat), 'opacity-50 cursor-not-allowed': isFormDisabled}">
                                    <input type="checkbox" :checked="isSyaratSelected(syarat)" @change="toggleSyarat(syarat)" :disabled="isFormDisabled" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                    <span class="ml-3 text-sm text-gray-900 select-none">{{ syarat }}</span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.syarat" />
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex-shrink-0 flex items-center justify-end p-4 sm:p-6 bg-gray-50 border-t space-x-3">
                    <template v-if="modalMode === 'create'">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton @click="createSurat" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan</PrimaryButton>
                    </template>
                    <template v-else-if="modalMode === 'view'">
                        <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                        <PrimaryButton @click="switchToEditMode">Edit</PrimaryButton>
                    </template>
                    <template v-else-if="modalMode === 'edit'">
                        <SecondaryButton @click="cancelEdit">Batal</SecondaryButton>
                        <PrimaryButton @click="updateSurat" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                    </template>
                </div>
            </div>
        </Modal>

        <DeleteModal :show="showDeleteModal" :surat="selectedSuratForDelete" @close="closeDeleteModal" />
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
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    jenisSurat: Array,
    templateOptions: Array,
});

// Modal states
const showModal = ref(false);
const modalMode = ref('create');
const selectedSurat = ref(null);
const showDeleteModal = ref(false);
const selectedSuratForDelete = ref(null);

// Filter states
const searchQuery = ref('');
const filterTemplate = ref('');
const filterSyarat = ref('');
const sortOrder = ref('asc');
const sortField = ref('nama');
const sortDirection = ref('asc');

const form = useForm({
    id: null,
    nama_surat: '',
    deskripsi: '',
    template_surat: '',
    syarat: [],
});

const syaratOptions = [
    'KTP', 'Kartu Keluarga', 'Akta Lahir', 'Surat RT/RW', 'Foto Usaha', 'Proposal Kegiatan'
];

const flash = computed(() => usePage().props.flash);
const isFormDisabled = computed(() => modalMode.value === 'view');
const modalTitle = computed(() => {
    if (modalMode.value === 'create') return 'Tambah Jenis Surat Baru';
    if (modalMode.value === 'view') return 'Detail Jenis Surat';
    return 'Edit Jenis Surat';
});

// Computed property untuk filter dan sort
const filteredJenisSurat = computed(() => {
    let result = [...props.jenisSurat];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(surat => 
            surat.nama_surat?.toLowerCase().includes(query) ||
            surat.deskripsi?.toLowerCase().includes(query)
        );
    }

    // Apply template filter
    if (filterTemplate.value) {
        if (filterTemplate.value === 'ada') {
            result = result.filter(surat => surat.template_surat);
        } else if (filterTemplate.value === 'tidak_ada') {
            result = result.filter(surat => !surat.template_surat);
        } else {
            result = result.filter(surat => surat.template_surat === filterTemplate.value);
        }
    }

    // Apply syarat filter
    if (filterSyarat.value) {
        result = result.filter(surat => 
            surat.syarat && surat.syarat.includes(filterSyarat.value)
        );
    }

    // Apply sorting
    result.sort((a, b) => {
        let comparison = 0;
        
        switch (sortOrder.value) {
            case 'asc':
                comparison = (a.nama_surat || '').localeCompare(b.nama_surat || '');
                break;
            case 'desc':
                comparison = (b.nama_surat || '').localeCompare(a.nama_surat || '');
                break;
            case 'newest':
                comparison = new Date(b.created_at || 0) - new Date(a.created_at || 0);
                break;
            case 'oldest':
                comparison = new Date(a.created_at || 0) - new Date(b.created_at || 0);
                break;
        }
        
        return comparison;
    });

    return result;
});

// Functions
const setSortOrder = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        sortOrder.value = sortDirection.value;
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
        sortOrder.value = 'asc';
    }
};

const clearFilters = () => {
    searchQuery.value = '';
    filterTemplate.value = '';
    filterSyarat.value = '';
    sortOrder.value = 'asc';
    sortField.value = 'nama';
    sortDirection.value = 'asc';
};

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    showModal.value = true;
};

const openViewModal = (surat) => {
    modalMode.value = 'view';
    selectedSurat.value = { ...surat };
    form.defaults({ ...surat }).reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
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
    form.reset();
    modalMode.value = 'view';
};

const openDeleteModal = (surat) => {
    selectedSuratForDelete.value = surat;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedSuratForDelete.value = null;
};

const toggleSyarat = (syarat) => {
    const index = form.syarat.indexOf(syarat);
    if (index > -1) form.syarat.splice(index, 1);
    else form.syarat.push(syarat);
};

const isSyaratSelected = (syarat) => form.syarat?.includes(syarat);
</script>