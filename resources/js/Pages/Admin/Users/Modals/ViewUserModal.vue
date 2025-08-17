<!-- resources/js/Pages/Admin/Users/Modals/ViewUserModal.vue -->

<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    user: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'edit']);

const closeModal = () => {
    emit('close');
};

const triggerEdit = () => {
    emit('edit', props.user);
};

// Helper untuk menampilkan data atau strip jika kosong
const displayData = (data) => data || '-';
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <!-- Wrapper untuk layout flexbox -->
        <div class="flex flex-col max-h-[90vh]">

            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-5 text-white rounded-t-lg">
                <h2 class="text-xl font-bold">Detail Pengguna</h2>
                <p class="text-gray-300 text-sm mt-1">Informasi lengkap untuk <span class="font-semibold">{{ user.name }}</span>.</p>
            </div>

            <!-- Konten Detail yang bisa di-scroll -->
            <div class="px-6 py-6 bg-gray-50 flex-grow overflow-y-auto">
                <div class="space-y-8">
                    <!-- Data Pribadi Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <label class="font-semibold text-gray-600">Nama Lengkap</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.name) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">NIK</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.nik) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">Tempat, Tanggal Lahir</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.tempat_lahir) }}, {{ displayData(user.tanggal_lahir) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">Jenis Kelamin</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.jenis_kelamin) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">Agama</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.agama) }}</p>
                            </div>
                             <div>
                                <label class="font-semibold text-gray-600">Status Perkawinan</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.status_perkawinan) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">Pekerjaan</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.pekerjaan) }}</p>
                            </div>
                             <div>
                                <label class="font-semibold text-gray-600">Kewarganegaraan</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.kewarganegaraan) }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="font-semibold text-gray-600">Alamat</label>
                                <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ displayData(user.address) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Akun Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Akun & Kontak</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <label class="font-semibold text-gray-600">Email</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.email) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">No. Telepon</label>
                                <p class="text-gray-900 mt-1">{{ displayData(user.phone) }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-600">Role</label>
                                <p class="text-gray-900 mt-1 capitalize">{{ displayData(user.role) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Tetap -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 rounded-b-lg">
                <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                <PrimaryButton @click="triggerEdit">Edit Pengguna</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
