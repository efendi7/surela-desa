<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Users, Plus, Pencil, Trash2, Eye, Phone, Mail, MapPin } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import PerangkatDesaFormModal from './Partials/PerangkatDesaFormModal.vue';

const props = defineProps({
    perangkatDesa: Object,
    jabatanOptions: Array,
});

const showFormModal = ref(false);
const showDeleteModal = ref(false);
const selectedPerangkat = ref(null);
const isEdit = ref(false);

const openCreateModal = () => {
    selectedPerangkat.value = null;
    isEdit.value = false;
    showFormModal.value = true;
};

const openEditModal = (perangkat) => {
    selectedPerangkat.value = perangkat;
    isEdit.value = true;
    showFormModal.value = true;
};

const openDeleteModal = (perangkat) => {
    selectedPerangkat.value = perangkat;
    showDeleteModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    selectedPerangkat.value = null;
    isEdit.value = false;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedPerangkat.value = null;
};

const submitDelete = () => {
    useForm({}).delete(route('admin.perangkat-desa.destroy', selectedPerangkat.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedPerangkat.value = null;
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getStatusBadge = (status) => {
    const badges = {
        'aktif': 'bg-green-100 text-green-800',
        'non_aktif': 'bg-red-100 text-red-800',
        'cuti': 'bg-yellow-100 text-yellow-800',
        'mutasi': 'bg-blue-100 text-blue-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getKepegawaianBadge = (status) => {
    const badges = {
        'PNS': 'bg-blue-100 text-blue-800',
        'PPPK': 'bg-purple-100 text-purple-800',
        'Honorer': 'bg-gray-100 text-gray-800',
        'Kontrak': 'bg-orange-100 text-orange-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Manajemen Perangkat Desa" />
    
    <AuthenticatedLayout>
        <!-- Flash Message Component -->
        <FlashMessage />
        
        <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Perangkat Desa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header Section -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Data Perangkat Desa</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Total {{ perangkatDesa.total }} perangkat desa terdaftar
                                </p>
                            </div>
                            <PrimaryButton @click="openCreateModal" class="flex items-center space-x-2">
                                <Plus class="h-4 w-4" />
                                <span>Tambah Perangkat</span>
                            </PrimaryButton>
                        </div>

                        <!-- Table Section -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Perangkat
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jabatan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Kepegawaian
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kontak
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Masa Jabatan
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="perangkat in perangkatDesa.data" :key="perangkat.id" class="hover:bg-gray-50">
                                        <!-- Perangkat Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img v-if="perangkat.foto" 
                                                         class="h-10 w-10 rounded-full object-cover" 
                                                         :src="perangkat.foto_url" 
                                                         :alt="perangkat.nama">
                                                    <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <Users class="h-5 w-5 text-gray-600" />
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ perangkat.nama }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ perangkat.nik ? `NIK: ${perangkat.nik}` : 'NIK: -' }}
                                                    </div>
                                                    <div v-if="perangkat.nip" class="text-sm text-gray-500">
                                                        NIP: {{ perangkat.nip }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Jabatan -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">
                                                {{ perangkat.jabatan_desa?.nama_jabatan || '-' }}
                                            </div>
                                            <div v-if="perangkat.pangkat_golongan" class="text-sm text-gray-500">
                                                {{ perangkat.pangkat_golongan }}
                                            </div>
                                            <div v-if="perangkat.pendidikan_terakhir" class="text-sm text-gray-500">
                                                {{ perangkat.pendidikan_terakhir }}
                                            </div>
                                        </td>

                                        <!-- Status Kepegawaian -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getKepegawaianBadge(perangkat.status_kepegawaian)
                                            ]">
                                                {{ perangkat.status_kepegawaian }}
                                            </span>
                                        </td>

                                        <!-- Kontak -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div v-if="perangkat.telepon" class="flex items-center mb-1">
                                                    <Phone class="h-3 w-3 text-gray-400 mr-1" />
                                                    {{ perangkat.telepon }}
                                                </div>
                                                <div v-if="perangkat.email" class="flex items-center">
                                                    <Mail class="h-3 w-3 text-gray-400 mr-1" />
                                                    {{ perangkat.email }}
                                                </div>
                                                <div v-if="!perangkat.telepon && !perangkat.email" class="text-gray-500">
                                                    -
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getStatusBadge(perangkat.status_jabatan)
                                            ]">
                                                {{ perangkat.status_jabatan === 'aktif' ? 'Aktif' : 
                                                   perangkat.status_jabatan === 'non_aktif' ? 'Non-Aktif' : 
                                                   perangkat.status_jabatan === 'cuti' ? 'Cuti' : 'Mutasi' }}
                                            </span>
                                        </td>

                                        <!-- Masa Jabatan -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ formatDate(perangkat.tanggal_mulai) }}</div>
                                            <div class="text-gray-500">
                                                {{ perangkat.tanggal_selesai ? `s/d ${formatDate(perangkat.tanggal_selesai)}` : 'Aktif' }}
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button @click="openEditModal(perangkat)" 
                                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                                    <Pencil class="h-4 w-4" />
                                                </button>
                                                <button @click="openDeleteModal(perangkat)" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                                                    <Trash2 class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="perangkatDesa.data.length === 0" class="text-center py-12">
                            <Users class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada perangkat desa</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Mulai dengan menambahkan perangkat desa pertama.
                            </p>
                            <div class="mt-6">
                                <PrimaryButton @click="openCreateModal" class="inline-flex items-center">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Tambah Perangkat
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="perangkatDesa.data.length > 0" class="mt-6">
                            <Pagination :links="perangkatDesa.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Modal (Create/Edit) -->
        <PerangkatDesaFormModal
            :show="showFormModal"
            :isEdit="isEdit"
            :selectedPerangkat="selectedPerangkat"
            :jabatanOptions="jabatanOptions"
            @close="closeFormModal"
        />

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <Trash2 class="h-6 w-6 text-red-600" />
                    </div>
                </div>

                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Hapus Perangkat Desa
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus 
                            <span class="font-semibold">{{ selectedPerangkat?.nama }}</span>?
                            <br>
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 flex flex-col-reverse sm:flex-row sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
                    <SecondaryButton @click="closeDeleteModal" class="w-full sm:w-auto justify-center">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="submitDelete" class="w-full sm:w-auto justify-center">
                        Ya, Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>