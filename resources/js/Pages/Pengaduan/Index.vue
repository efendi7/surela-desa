<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
// Partials
import PengaduanAktif from './Partials/PengaduanAktif.vue';
import RiwayatPengaduan from './Partials/RiwayatPengaduan.vue';
import PengaduanModal from './Partials/PengaduanModal.vue';

const props = defineProps({
    pengaduanAktif: Array,
    riwayatPengaduan: Array,
    isProfileComplete: Boolean,
});

// === STATE MANAGEMENT ===
const modalState = ref({ show: false, mode: 'create', selectedData: null });
const confirmModalState = ref({ show: false, title: '', message: '', action: null });
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
const submitNewPengaduan = (form) => {
    form.post(route('pengaduan.store'), {
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

const handleCancel = (laporan) => {
    openConfirmModal(
        'Batalkan Laporan',
        'Apakah Anda yakin ingin membatalkan laporan ini? Laporan yang dikirim tidak dapat dikembalikan.',
        () => {
            // Asumsi ada route untuk cancel, jika tidak ada bisa dibuat atau diganti destroy
            deleteForm.delete(route('pengaduan.destroy', laporan.id), { preserveScroll: true });
        }
    );
};

const handleDeleteHistory = (laporan) => {
    openConfirmModal(
        'Hapus Riwayat',
        'Apakah Anda yakin ingin menghapus riwayat laporan ini? Tindakan ini tidak dapat dikembalikan.',
        () => {
            // Asumsi ada route untuk delete history
            deleteForm.delete(route('pengaduan.destroy', laporan.id), { preserveScroll: true });
        }
    );
};
</script>

<template>
    <Head title="Pengaduan Warga" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan & Pengaduan Warga</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <FlashMessage />

                <div v-if="!isProfileComplete" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded" role="alert">
                    <p class="font-bold">Profil Belum Lengkap</p>
                    <p>Anda harus melengkapi data profil (seperti NIK dan Alamat) sebelum membuat laporan.</p>
                     <Link :href="route('profile.edit')" class="mt-2 inline-block text-sm font-semibold text-yellow-800 hover:text-yellow-900">
                        Lengkapi Profil Sekarang &rarr;
                    </Link>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Laporan Aktif Anda</h3>
                        <PrimaryButton @click="openModal('create')" :disabled="!isProfileComplete" :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }">
                            Buat Laporan Baru
                        </PrimaryButton>
                    </div>
                    <PengaduanAktif 
                        :pengaduan-aktif="pengaduanAktif"
                        @view-detail="data => openModal('view', data)"
                        @cancel="handleCancel"
                    />
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                     <h3 class="text-lg font-semibold text-gray-900 mb-6">Riwayat Laporan</h3>
                    <RiwayatPengaduan
                        :riwayat-pengaduan="riwayatPengaduan"
                        @view-detail="data => openModal('view', data)"
                        @delete-history="handleDeleteHistory"
                    />
                </div>
            </div>
        </div>

        <PengaduanModal 
            :show="modalState.show"
            :mode="modalState.mode"
            :pengaduan="modalState.selectedData"
            @close="closeModal"
            @submit="submitNewPengaduan"
        />

        <ConfirmationModal
            :show="confirmModalState.show"
            :title="confirmModalState.title"
            :message="confirmModalState.message"
            @close="closeConfirmModal"
            @confirm="executeConfirmAction"
        />
    </AuthenticatedLayout>
</template>