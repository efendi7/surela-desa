<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    umkms: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const confirmingUmkmDeletion = ref(false);
const umkmToDelete = ref(null);
const showStatusModal = ref(false);
const umkmToUpdate = ref(null);

const deleteForm = useForm({});
const statusForm = useForm({
    status: '',
    catatan_admin: '',
});

const stats = computed(() => {
    const data = props.umkms.data || [];
    const total = props.umkms.total || 0;
    const pending = data.filter(umkm => umkm.status === 'pending').length;
    
    return { total, pending };
});

const openStatusModal = (umkm) => {
    umkmToUpdate.value = umkm;
    statusForm.status = umkm.status;
    statusForm.catatan_admin = umkm.catatan_admin || '';
    showStatusModal.value = true;
    statusForm.clearErrors();
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    umkmToUpdate.value = null;
    statusForm.reset();
    statusForm.clearErrors();
};

const submitStatusUpdate = () => {
    if (!umkmToUpdate.value) {
        console.error('No UMKM selected for status update');
        return;
    }

    statusForm.patch(route('admin.umkm.update.status', umkmToUpdate.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeStatusModal();
            router.visit(route('admin.umkm.index'), {
                preserveState: true,
                preserveScroll: true,
            });
        },
        onError: (errors) => {
            console.error('Status update failed:', errors);
            // Keep modal open to show validation errors
        },
    });
};

const confirmUmkmDeletion = (umkm) => {
    umkmToDelete.value = umkm;
    confirmingUmkmDeletion.value = true;
};

const deleteUmkm = () => {
    deleteForm.delete(route('admin.umkm.destroy', umkmToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: (errors) => {
            console.error('Delete failed:', errors);
        },
        onFinish: () => {
            umkmToDelete.value = null;
        },
    });
};

const closeDeleteModal = () => {
    confirmingUmkmDeletion.value = false;
    umkmToDelete.value = null;
    deleteForm.reset();
};

const getFotoUrl = (path) => {
    return path ? `/storage/${path.replace('public/', '')}` : 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image';
};

const formatDate = (dateString) => {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        timeZone: 'Asia/Jakarta'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const statusClass = (status) => {
    return 'bg-yellow-100 text-yellow-800 border-yellow-200';
};

const formatStatus = (status) => {
    return 'Menunggu Verifikasi';
};
</script>

<template>
    <Head title="Kelola UMKM - Pending" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola UMKM - Pending</h2>
                <p class="text-sm text-gray-600 mt-1">Verifikasi pendaftaran UMKM warga yang menunggu</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Total UMKM Pending</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ stats.total }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ stats.pending }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                    <div class="p-6">
                        <!-- Tampilan jika ada data UMKM -->
                        <div v-if="umkms.data.length > 0">
                            <!-- Desktop Table View -->
                            <div class="hidden lg:block overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Usaha</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Aksi</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="umkm in umkms.data" :key="umkm.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-16 w-16">
                                                        <img 
                                                            class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200" 
                                                            :src="getFotoUrl(umkm.foto_produk)" 
                                                            :alt="`Foto produk ${umkm.nama_usaha}`"
                                                            @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                                                        >
                                                    </div>
                                                    <div class="ml-4 min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 truncate">{{ umkm.nama_usaha }}</div>
                                                        <div class="text-sm text-gray-500">{{ umkm.kategori_usaha }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ umkm.user?.name || 'N/A' }}</div>
                                                <div class="text-xs text-gray-400 mt-1">{{ umkm.nomor_telepon }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full border" :class="statusClass(umkm.status)">
                                                    {{ formatStatus(umkm.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(umkm.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <Link 
                                                        :href="route('admin.umkm.show', umkm.id)" 
                                                        class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Lihat
                                                    </Link>
                                                    <button 
                                                        @click="openStatusModal(umkm)" 
                                                        class="inline-flex items-center px-3 py-1 border border-indigo-300 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Status
                                                    </button>
                                                    <button 
                                                        @click="confirmUmkmDeletion(umkm)" 
                                                        class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="lg:hidden space-y-4">
                                <div v-for="umkm in umkms.data" :key="umkm.id" class="border border-gray-200 rounded-lg p-4 space-y-3">
                                    <div class="flex items-start space-x-3">
                                        <img 
                                            class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200" 
                                            :src="getFotoUrl(umkm.foto_produk)" 
                                            :alt="`Foto produk ${umkm.nama_usaha}`"
                                            @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ umkm.nama_usaha }}</h3>
                                            <p class="text-sm text-gray-500">{{ umkm.kategori_usaha }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ umkm.user?.name || 'N/A' }}</p>
                                        </div>
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full border shrink-0" :class="statusClass(umkm.status)">
                                            {{ formatStatus(umkm.status) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">{{ formatDate(umkm.created_at) }}</span>
                                        <div class="flex space-x-2">
                                            <Link 
                                                :href="route('admin.umkm.show', umkm.id)" 
                                                class="inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50"
                                            >
                                                Lihat
                                            </Link>
                                            <button 
                                                @click="openStatusModal(umkm)" 
                                                class="inline-flex items-center px-2 py-1 border border-indigo-300 rounded text-xs font-medium text-indigo-700 bg-indigo-50"
                                            >
                                                Status
                                            </button>
                                            <button 
                                                @click="confirmUmkmDeletion(umkm)" 
                                                class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                <Pagination :links="umkms.links" />
                            </div>
                        </div>

                        <!-- No Data -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-700">Belum Ada UMKM Pending</h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Belum ada warga yang mendaftarkan UMKM mereka yang menunggu verifikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Update Status -->
        <Modal :show="showStatusModal" @close="closeStatusModal" max-width="2xl">
            <form @submit.prevent="submitStatusUpdate" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Update Status UMKM</h2>
                    <button type="button" @click="closeStatusModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div v-if="umkmToUpdate" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <img 
                            class="h-12 w-12 rounded-lg object-cover border-2 border-gray-200" 
                            :src="getFotoUrl(umkmToUpdate.foto_produk)" 
                            :alt="`Foto produk ${umkmToUpdate.nama_usaha}`"
                            @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                        >
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ umkmToUpdate.nama_usaha }}</h3>
                            <p class="text-sm text-gray-500">{{ umkmToUpdate.kategori_usaha }}</p>
                            <p class="text-xs text-gray-400">{{ umkmToUpdate.user?.name || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="status" value="Status UMKM" />
                        <select
                            id="status"
                            v-model="statusForm.status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">Pilih Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <InputError class="mt-2" :message="statusForm.errors.status" />
                    </div>

                    <div>
                        <InputLabel for="catatan_admin" value="Catatan Admin" />
                        <TextArea
                            id="catatan_admin"
                            v-model="statusForm.catatan_admin"
                            class="mt-1 block w-full"
                            rows="4"
                            placeholder="Berikan catatan untuk warga (opsional, wajib jika status ditolak)"
                            :class="statusForm.status === 'ditolak' ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''"
                            :required="statusForm.status === 'ditolak'"
                        />
                        <InputError class="mt-2" :message="statusForm.errors.catatan_admin" />
                        <p v-if="statusForm.status === 'ditolak'" class="mt-1 text-xs text-red-600">
                            Catatan wajib diisi untuk status ditolak
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 space-x-3">
                    <SecondaryButton @click="closeStatusModal">
                        Batal
                    </SecondaryButton>
                    <PrimaryButton 
                        :class="{ 'opacity-25': statusForm.processing }" 
                        :disabled="statusForm.processing"
                    >
                        <span v-if="statusForm.processing">Memperbarui...</span>
                        <span v-else>Perbarui Status</span>
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <!-- Modal Konfirmasi Hapus -->
        <Modal :show="confirmingUmkmDeletion" @close="closeDeleteModal" max-width="md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Konfirmasi Penghapusan</h2>
                    <button type="button" @click="closeDeleteModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div v-if="umkmToDelete" class="mb-6">
                    <div class="flex items-center space-x-4 p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800">
                                Apakah Anda yakin ingin menghapus UMKM ini?
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p><strong>Nama UMKM:</strong> {{ umkmToDelete.nama_usaha }}</p>
                                <p><strong>Pemilik:</strong> {{ umkmToDelete.nama_pemilik }}</p>
                                <p><strong>Status:</strong> {{ formatStatus(umkmToDelete.status) }}</p>
                            </div>
                            <p class="mt-3 text-xs text-red-600">
                                <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data UMKM dan foto produk akan dihapus secara permanen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <SecondaryButton @click="closeDeleteModal">
                        Batal
                    </SecondaryButton>
                    <DangerButton 
                        @click="deleteUmkm" 
                        :class="{ 'opacity-25': deleteForm.processing }" 
                        :disabled="deleteForm.processing"
                    >
                        <span v-if="deleteForm.processing">Menghapus...</span>
                        <span v-else>Ya, Hapus UMKM</span>
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.transition-colors {
    transition-property: background-color, border-color, color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.rounded-full {
    border-radius: 9999px;
}

img {
    transition: all 0.3s ease;
}

img:hover {
    transform: scale(1.05);
}

.opacity-25 {
    opacity: 0.25;
}

button:focus,
select:focus,
textarea:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .print-friendly {
        background: white !important;
        color: black !important;
    }
}

@media (max-width: 768px) {
    .mobile-stack {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-full-width {
        width: 100%;
    }
}
</style>