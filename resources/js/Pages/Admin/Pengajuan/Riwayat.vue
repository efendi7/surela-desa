<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

// PERUBAHAN 1: Menerima prop 'riwayatPengajuan' dari controller
const props = defineProps({
    riwayatPengajuan: Array,
});

const showDetailModal = ref(false);
const selectedPengajuan = ref(null);

// PERUBAHAN 2: Fungsi openDetailModal disederhanakan, tidak perlu mengisi form
const openDetailModal = (pengajuan) => {
    selectedPengajuan.value = pengajuan;
    showDetailModal.value = true;
};

const closeModal = () => {
    showDetailModal.value = false;
    selectedPengajuan.value = null;
};

// Fungsi getStatusClass tetap sama, tidak perlu diubah
const getStatusClass = (status) => ({
    'bg-yellow-100 text-yellow-800': status === 'pending',
    'bg-blue-100 text-blue-800': status === 'diproses',
    'bg-green-100 text-green-800': status === 'selesai',
    'bg-red-100 text-red-800': status === 'ditolak',
});

// PERUBAHAN 3: Form dan fungsi updateStatus dihapus karena tidak diperlukan lagi
</script>

<template>
    <Head title="Riwayat Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pengajuan Surat</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Pemohon</th>
                                        <th class="py-2 px-4 text-left">Jenis Surat</th>
                                        <th class="py-2 px-4 text-left">Tanggal</th>
                                        <th class="py-2 px-4 text-left">Status Akhir</th>
                                        <th class="py-2 px-4 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="riwayatPengajuan.length === 0">
                                        <td colspan="5" class="text-center py-4 text-gray-500">Belum ada riwayat pengajuan.</td>
                                    </tr>
                                    <tr v-for="pengajuan in riwayatPengajuan" :key="pengajuan.id" class="border-b">
                                        <td class="py-2 px-4">{{ pengajuan.user.name }}</td>
                                        <td class="py-2 px-4">{{ pengajuan.jenis_surat.nama_surat }}</td>
                                        <td class="py-2 px-4">{{ new Date(pengajuan.created_at).toLocaleDateString('id-ID') }}</td>
                                        <td class="py-2 px-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getStatusClass(pengajuan.status)">{{ pengajuan.status }}</span>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button @click="openDetailModal(pengajuan)" class="text-indigo-600 hover:text-indigo-900 mr-4">Detail</button>
                                            <a v-if="pengajuan.status === 'selesai'" :href="route('admin.proses.cetak', pengajuan.id)" target="_blank" class="text-green-600 hover:text-green-900">
                                                Cetak
                                            </a>
                                            <span v-else class="text-gray-400 cursor-not-allowed">Cetak</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showDetailModal" @close="closeModal">
            <div class="p-6" v-if="selectedPengajuan">
                <h2 class="text-lg font-medium text-gray-900">Detail Riwayat: {{ selectedPengajuan.jenis_surat.nama_surat }}</h2>
                
                <div class="mt-4 border-t pt-4 space-y-4 text-sm">
                    <p><strong>Pemohon:</strong> {{ selectedPengajuan.user.name }}</p>
                    <p><strong>Status Akhir:</strong> 
                        <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getStatusClass(selectedPengajuan.status)">
                            {{ selectedPengajuan.status }}
                        </span>
                    </p>
                     <div v-if="selectedPengajuan.keterangan_admin">
                        <strong>Keterangan Admin:</strong>
                        <p class="mt-1 p-2 bg-gray-100 rounded-md whitespace-pre-wrap">{{ selectedPengajuan.keterangan_admin }}</p>
                    </div>
                    <div>
                        <strong>Lampiran:</strong>
                        <ul class="list-disc list-inside ml-4">
                            <li v-for="(path, name) in selectedPengajuan.lampiran" :key="name">
                                <a :href="'/storage/' + path" target="_blank" class="text-indigo-600 hover:underline">{{ name.replace(/_/g, ' ') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>