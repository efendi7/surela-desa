<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    jenisSuratTersedia: Array,
    riwayatPengajuan: Array,
});

// --- STATE MANAGEMENT ---
const showModal = ref(false);
const modalMode = ref('create'); // Mode bisa: 'create', 'view', 'delete'
const selectedPengajuan = ref(null);

const form = useForm({
    jenis_surat_id: '',
    lampiran: {}, // Diubah menjadi object untuk menampung file
});

// --- COMPUTED PROPERTIES ---
const flash = computed(() => usePage().props.flash);
const modalTitle = computed(() => {
    if (modalMode.value === 'create') return 'Buat Pengajuan Surat Baru';
    if (modalMode.value === 'view') return 'Detail Pengajuan Surat';
    return 'Batalkan Pengajuan';
});
const selectedJenisSurat = computed(() => {
    if (form.jenis_surat_id) {
        return props.jenisSuratTersedia.find(s => s.id === form.jenis_surat_id);
    }
    return null;
});

// --- MODAL FUNCTIONS ---
const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.lampiran = {}; // Reset object lampiran secara eksplisit
    showModal.value = true;
};

const openViewModal = (pengajuan) => {
    modalMode.value = 'view';
    selectedPengajuan.value = pengajuan;
    showModal.value = true;
};

const openDeleteModal = (pengajuan) => {
    modalMode.value = 'delete';
    selectedPengajuan.value = pengajuan;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedPengajuan.value = null;
};

// --- FORM ACTIONS ---
const submitPengajuan = () => {
    // Inertia otomatis mengubah ke multipart/form-data jika ada File object
    form.post(route('pengajuan.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const cancelPengajuan = () => {
    if (!selectedPengajuan.value) return;
    const deleteForm = useForm({});
    deleteForm.delete(route('pengajuan.destroy', selectedPengajuan.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

// --- HELPERS ---
const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        diproses: 'bg-blue-100 text-blue-800',
        selesai: 'bg-green-100 text-green-800',
        ditolak: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const slugify = (text) => {
    return text.toLowerCase().replace(/\s+/g, '_').replace(/[^\w-]+/g, '');
};
</script>

<template>
    <Head title="Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Surat Anda</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <PrimaryButton @click="openCreateModal">Ajukan Surat Baru</PrimaryButton>
                        </div>

                        <div v-if="flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ flash.success }}</span>
                        </div>
                        <div v-if="flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ flash.error }}</span>
                        </div>

                        <h3 class="text-lg font-medium mb-4">Riwayat Pengajuan Anda</h3>

                        <!-- Responsive Table/Cards -->
                        <div class="overflow-x-auto">
                            <!-- Desktop Table -->
                            <table class="hidden md:table min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Jenis Surat</th>
                                        <th class="py-2 px-4 text-left">Tanggal Pengajuan</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pengajuan in riwayatPengajuan" :key="pengajuan.id" class="border-b">
                                        <td class="py-2 px-4">{{ pengajuan.jenis_surat.nama_surat }}</td>
                                        <td class="py-2 px-4">{{ new Date(pengajuan.created_at).toLocaleDateString('id-ID') }}</td>
                                        <td class="py-2 px-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getStatusClass(pengajuan.status)">
                                                {{ pengajuan.status }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button @click="openViewModal(pengajuan)" class="text-indigo-600 hover:text-indigo-900 mr-4">Detail</button>
                                            <button v-if="pengajuan.status === 'pending'" @click="openDeleteModal(pengajuan)" class="text-red-600 hover:text-red-900">Batalkan</button>
                                        </td>
                                    </tr>
                                    <tr v-if="riwayatPengajuan.length === 0">
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">Anda belum pernah membuat pengajuan.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Mobile Cards -->
                            <div class="md:hidden space-y-4">
                                <div v-for="pengajuan in riwayatPengajuan" :key="pengajuan.id" class="bg-white border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-semibold">{{ pengajuan.jenis_surat.nama_surat }}</h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getStatusClass(pengajuan.status)">{{ pengajuan.status }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Diajukan pada: {{ new Date(pengajuan.created_at).toLocaleDateString('id-ID') }}</p>
                                    <div class="flex justify-end space-x-2 mt-4 pt-2 border-t">
                                        <button @click="openViewModal(pengajuan)" class="text-indigo-600 text-sm font-medium">Detail</button>
                                        <button v-if="pengajuan.status === 'pending'" @click="openDeleteModal(pengajuan)" class="text-red-600 text-sm font-medium">Batalkan</button>
                                    </div>
                                </div>
                                <div v-if="riwayatPengajuan.length === 0" class="text-center py-8 text-gray-500">Anda belum pernah membuat pengajuan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Create, View, Delete -->
        <Modal :show="showModal" @close="closeModal">
            <div class="flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="p-6 border-b">
                    <h2 class="text-lg font-medium text-gray-900">{{ modalTitle }}</h2>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 flex-1 overflow-y-auto">
                    <!-- Create Modal Content -->
                    <div v-if="modalMode === 'create'">
                        <p class="text-sm text-gray-600 mb-4">Pilih jenis surat yang ingin Anda ajukan.</p>
                        <div>
                            <InputLabel for="jenis_surat_id" value="Pilih Jenis Surat" />
                            <select id="jenis_surat_id" v-model="form.jenis_surat_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled>-- Pilih Surat --</option>
                                <option v-for="surat in jenisSuratTersedia" :key="surat.id" :value="surat.id">{{ surat.nama_surat }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.jenis_surat_id" />
                        </div>
                        
                        <!-- Dynamic File Upload Section -->
                        <div v-if="selectedJenisSurat && selectedJenisSurat.syarat.length" class="mt-6 pt-4 border-t space-y-4">
                            <h4 class="font-semibold text-gray-700">Unggah Persyaratan (Wajib PDF, maks 2MB)</h4>
                            <div v-for="syarat in selectedJenisSurat.syarat" :key="syarat">
                                <InputLabel :for="slugify(syarat)" :value="syarat" />
                                <input 
                                    type="file" 
                                    :id="slugify(syarat)"
                                    @input="form.lampiran[slugify(syarat)] = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    accept=".pdf"
                                >
                                <InputError class="mt-2" :message="form.errors['lampiran.' + slugify(syarat)]" />
                            </div>
                        </div>
                    </div>

                    <!-- View Modal Content -->
                    <div v-if="modalMode === 'view' && selectedPengajuan" class="space-y-4 text-sm">
                        <div>
                            <h4 class="font-semibold text-gray-600">Jenis Surat</h4>
                            <p class="text-gray-800">{{ selectedPengajuan.jenis_surat.nama_surat }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Tanggal Pengajuan</h4>
                            <p class="text-gray-800">{{ new Date(selectedPengajuan.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600">Status</h4>
                            <p class="capitalize text-gray-800">{{ selectedPengajuan.status }}</p>
                        </div>
                        <div v-if="selectedPengajuan.keterangan_admin">
                            <h4 class="font-semibold text-gray-600">Keterangan dari Admin</h4>
                            <p class="p-2 bg-gray-100 rounded text-gray-800">{{ selectedPengajuan.keterangan_admin }}</p>
                        </div>
                         <div>
                            <h4 class="font-semibold text-gray-600">Lampiran Terunggah</h4>
                            <div v-if="selectedPengajuan.lampiran && Object.keys(selectedPengajuan.lampiran).length > 0" class="mt-2 space-y-1">
                                <div v-for="(path, name) in selectedPengajuan.lampiran" :key="name">
                                    <a :href="'/storage/' + path" target="_blank" class="text-indigo-600 hover:underline flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        Lihat {{ name.replace(/_/g, ' ') }}
                                    </a>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">Tidak ada lampiran.</p>
                        </div>
                    </div>

                    <!-- Delete Modal Content -->
                    <div v-if="modalMode === 'delete' && selectedPengajuan" class="mt-4">
                        <p class="text-sm text-gray-600">Anda yakin ingin membatalkan pengajuan untuk **{{ selectedPengajuan.jenis_surat.nama_surat }}**?</p>
                        <p class="text-sm text-gray-500 mt-2">Tindakan ini tidak dapat diurungkan.</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-6 bg-gray-50 border-t">
                    <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                    <PrimaryButton v-if="modalMode === 'create'" @click="submitPengajuan" class="ms-3" :disabled="form.processing">Kirim Pengajuan</PrimaryButton>
                    <DangerButton v-if="modalMode === 'delete'" @click="cancelPengajuan" class="ms-3">Ya, Batalkan</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
