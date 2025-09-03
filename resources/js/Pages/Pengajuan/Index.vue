<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

// Partials
import PengajuanAktif from './Partials/PengajuanAktif.vue';
import RiwayatPengajuan from './Partials/RiwayatPengajuan.vue';
import PengajuanModal from './Partials/PengajuanModal.vue';


const props = defineProps({
    jenisSuratTersedia: Array,
    pengajuanAktif: Array,
    riwayatPengajuan: Array,
    isProfileComplete: Boolean,
});

// === STATE MANAGEMENT ===
const modalState = ref({
    show: false,
    mode: 'create', // 'create' atau 'view'
    selectedData: null,
});

const confirmModalState = ref({
    show: false,
    title: '',
    message: '',
    action: null,
});

const deleteForm = useForm({});

// === MODAL HANDLERS ===
const openModal = (mode, data = null) => {
    modalState.value = { show: true, mode, selectedData: data };
};

const closeModal = () => {
    modalState.value.show = false;
};

const openConfirmModal = (title, message, action) => {
    confirmModalState.value = { show: true, title, message, action };
};

const closeConfirmModal = () => {
    confirmModalState.value.show = false;
};

// === ACTIONS ===
const submitNewPengajuan = (form) => {
    form.post(route('pengajuan.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const executeConfirmAction = () => {
    if (confirmModalState.value.action) {
        confirmModalState.value.action();
    }
    closeConfirmModal();
};

const handleCancel = (pengajuan) => {
    openConfirmModal(
        'Batalkan Pengajuan',
        'Apakah Anda yakin ingin membatalkan pengajuan ini? Tindakan ini tidak dapat dikembalikan.',
        () => {
            deleteForm.delete(route('pengajuan.destroy', pengajuan.id), {
                preserveScroll: true,
            });
        }
    );
};

const handleDeleteHistory = (pengajuan) => {
    openConfirmModal(
        'Hapus Riwayat',
        'Apakah Anda yakin ingin menghapus riwayat pengajuan ini? Ini tidak akan bisa dikembalikan.',
        () => {
            deleteForm.delete(route('pengajuan.destroy-riwayat', pengajuan.id), {
                preserveScroll: true,
            });
        }
    );
};
</script>

<template>
    <Head title="Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Surat Anda</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <FlashMessage />

                <div v-if="!isProfileComplete" class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded" role="alert">
                    <p class="font-bold">Profil Belum Lengkap</p>
                    <p class="mb-3">Anda harus melengkapi data profil Anda terlebih dahulu sebelum dapat mengajukan surat.</p>
                    <Link :href="route('profile.edit')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                        Ke Halaman Profil
                    </Link>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Pengajuan Sedang Diproses</h3>
                                    <p class="text-sm text-gray-500">Pengajuan dengan status pending atau sedang diproses</p>
                                </div>
                            </div>
                            <PrimaryButton @click="openModal('create')" :disabled="!isProfileComplete" :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }">
                                Ajukan Surat Baru
                            </PrimaryButton>
                        </div>
                        
                        <PengajuanAktif 
                            :pengajuan-aktif="pengajuanAktif"
                            @view-detail="data => openModal('view', data)"
                            @cancel="handleCancel"
                        />
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Pengajuan yang Selesai Diproses</h3>
                                <p class="text-sm text-gray-500">Riwayat pengajuan dengan status selesai atau ditolak</p>
                            </div>
                        </div>

                        <RiwayatPengajuan
                            :riwayat-pengajuan="riwayatPengajuan"
                            @view-detail="data => openModal('view', data)"
                            @delete-history="handleDeleteHistory"
                        />
                    </div>
                </div>
            </div>
        </div>

        <PengajuanModal 
            :show="modalState.show"
            :mode="modalState.mode"
            :pengajuan="modalState.selectedData"
            :jenis-surat-tersedia="jenisSuratTersedia"
            @close="closeModal"
            @submit="submitNewPengajuan"
        />

        <ConfirmationModal
            :show="confirmModalState.show"
            :title="confirmModalState.title"
            :message="confirmModalState.message"
            confirm-color="red"
            @close="closeConfirmModal"
            @confirm="executeConfirmAction"
        />

    </AuthenticatedLayout>
</template>