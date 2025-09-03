<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
  show: Boolean,
  mode: String, // 'create' or 'view'
  pengajuan: Object,
  jenisSuratTersedia: Array,
});

const emit = defineEmits(['close', 'submit']);

const { getStatusClass, getStatusIcon, formatDate, slugify } = usePengajuanUtils();

const form = useForm({
  jenis_surat_id: '',
  lampiran: {},
});

const modalTitle = computed(() => props.mode === 'create' ? 'Buat Pengajuan Surat Baru' : 'Detail Pengajuan Surat');

const selectedJenisSurat = computed(() => {
    if (form.jenis_surat_id) {
        return props.jenisSuratTersedia.find((s) => s.id === form.jenis_surat_id);
    }
    return null;
});

// Reset form ketika modal dibuka untuk mode 'create'
watch(() => props.show, (newVal) => {
    if (newVal && props.mode === 'create') {
        form.reset();
    }
});

const handleFileChange = (syarat, event) => {
    const file = event.target.files[0];
    const key = slugify(syarat);
    if (file) {
        form.lampiran[key] = file;
    } else {
        delete form.lampiran[key];
    }
};

const submit = () => {
    emit('submit', form);
};

// Helper function untuk mendapatkan status icon yang tepat
const getTimelineIcon = (status) => {
    switch (status) {
        case 'pending':
            return 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'diproses':
            return 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15';
        case 'selesai':
            return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'ditolak':
            return 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
        default:
            return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

const getTimelineBgColor = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-500';
        case 'diproses':
            return 'bg-blue-500';
        case 'selesai':
            return 'bg-green-500';
        case 'ditolak':
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
};

const getStatusBadgeClass = (status) => {
    const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize';
    switch (status) {
        case 'pending':
            return `${baseClasses} bg-yellow-100 text-yellow-800`;
        case 'diproses':
            return `${baseClasses} bg-blue-100 text-blue-800`;
        case 'selesai':
            return `${baseClasses} bg-green-100 text-green-800`;
        case 'ditolak':
            return `${baseClasses} bg-red-100 text-red-800`;
        default:
            return `${baseClasses} bg-gray-100 text-gray-800`;
    }
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="4xl">
        <div class="flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">{{ modalTitle }}</h2>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <!-- Create Mode -->
                <div v-if="mode === 'create'" class="p-6 space-y-6">
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Jenis Surat</h3>
                            <p class="text-sm text-gray-600">Pilih jenis surat yang ingin Anda ajukan dari daftar di bawah ini.</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="jenis_surat_id" value="Jenis Surat" />
                                <select
                                    id="jenis_surat_id"
                                    v-model="form.jenis_surat_id"
                                    class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm py-3 px-4"
                                >
                                    <option value="" disabled>-- Pilih Jenis Surat --</option>
                                    <option v-for="surat in jenisSuratTersedia" :key="surat.id" :value="surat.id">
                                        {{ surat.nama_surat }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jenis_surat_id" />
                            </div>
                        </div>
                    </div>

                    <!-- Document Requirements -->
                    <div v-if="selectedJenisSurat && selectedJenisSurat.syarat.length" class="bg-white rounded-lg p-6 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Dokumen yang Diperlukan</h3>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="flex-shrink-0 w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Persyaratan Upload</p>
                                        <ul class="mt-1 text-sm text-amber-700 list-disc list-inside space-y-1">
                                            <li>Format file harus PDF</li>
                                            <li>Ukuran maksimal 2MB per file</li>
                                            <li>Pastikan dokumen dapat dibaca dengan jelas</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6">
                            <div v-for="(syarat, index) in selectedJenisSurat.syarat" :key="syarat" class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">
                                        {{ index + 1 }}
                                    </span>
                                    <InputLabel :for="slugify(syarat)" :value="syarat" class="text-base" />
                                </div>
                                <div class="ml-8">
                                    <input
                                        :id="slugify(syarat)"
                                        type="file"
                                        accept=".pdf"
                                        @change="(event) => handleFileChange(syarat, event)"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer"
                                    />
                                    <InputError class="mt-2" :message="form.errors[`lampiran.${slugify(syarat)}`]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information -->
                    <div v-if="selectedJenisSurat" class="bg-white rounded-lg p-6 shadow-sm">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-base font-medium text-blue-900">Informasi Proses</h4>
                                <div class="mt-2 text-sm text-blue-800">
                                    <p>Setelah pengajuan disubmit, dokumen akan diverifikasi oleh admin. Anda akan mendapat notifikasi melalui email tentang status pengajuan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Mode -->
                <div v-if="mode === 'view' && pengajuan" class="p-6 space-y-6">
                    <!-- Header Info -->
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="space-y-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ pengajuan.jenis_surat.nama_surat }}</h3>
                                <div class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 space-y-1 sm:space-y-0 sm:space-x-4">
                                    <span>Diajukan pada {{ formatDate(pengajuan.created_at) }}</span>
                                    <span v-if="pengajuan.nomor_surat" class="font-medium">No. {{ pengajuan.nomor_surat }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" :class="pengajuan.status === 'selesai' ? 'text-green-500' : pengajuan.status === 'ditolak' ? 'text-red-500' : pengajuan.status === 'diproses' ? 'text-blue-500' : 'text-yellow-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(pengajuan.status)" />
                                </svg>
                                <span :class="getStatusBadgeClass(pengajuan.status)">
                                    {{ pengajuan.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div v-if="pengajuan.timeline && pengajuan.timeline.length" class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-6">Riwayat Status</h4>
                        <div class="flow-root">
                            <ul class="space-y-6">
                                <li v-for="(item, index) in pengajuan.timeline" :key="index" class="relative flex items-start space-x-4">
                                    <!-- Connector Line -->
                                    <div v-if="index !== pengajuan.timeline.length - 1" class="absolute top-10 left-6 w-0.5 h-full bg-gray-200 -ml-px"></div>
                                    
                                    <!-- Icon -->
                                    <div class="relative flex items-center justify-center w-12 h-12 rounded-full ring-4 ring-white shadow-sm" :class="getTimelineBgColor(item.status)">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getTimelineIcon(item.status)" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0 pb-6">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-base font-medium text-gray-900 capitalize">{{ item.status }}</p>
                                                <p class="text-sm text-gray-600 mt-1">{{ item.message }}</p>
                                            </div>
                                            <span class="text-sm text-gray-500 whitespace-nowrap ml-4">
                                                {{ formatDate(item.timestamp) }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Applicant Data -->
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Data Pemohon</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="text-base text-gray-900">{{ pengajuan.user.name }}</dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-base text-gray-900">{{ pengajuan.user.email }}</dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="text-base text-gray-900">{{ pengajuan.user.nik || '-' }}</dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="text-base text-gray-900">{{ pengajuan.user.address || pengajuan.user.alamat || '-' }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Documents di modal, ganti yang lama dengan ini -->
<div v-if="pengajuan.lampiran && Object.keys(pengajuan.lampiran).length > 0" class="bg-white rounded-lg p-6 shadow-sm">
    <h4 class="text-lg font-medium text-gray-900 mb-4">Dokumen Pendukung</h4>
    <div class="grid gap-4">
        <div v-for="(file, key) in pengajuan.lampiran" :key="key" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-base font-medium text-gray-900">{{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</p>
                    <p class="text-sm text-gray-500">{{ file.original_name || file.name || 'Document.pdf' }}</p>
                    <!-- Debug info - hapus setelah selesai debugging -->
                    <p class="text-xs text-gray-400">URL: {{ file.url }}</p>
                    <p class="text-xs text-gray-400">Exists: {{ file.exists ? 'Ya' : 'Tidak' }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <!-- Gunakan route yang benar untuk melihat lampiran -->
                <a 
                    :href="route('pengajuan.lampiran.view', { pengajuan: pengajuan.id, key: key })" 
                    target="_blank" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': !file.exists }"
                    :disabled="!file.exists"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Lihat
                </a>
                
                <!-- Tombol download jika diperlukan -->
            </div>
        </div>
    </div>
</div>

                    <!-- Admin Notes -->
                    <div v-if="pengajuan.catatan_admin || pengajuan.keterangan_admin" class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Catatan Admin</h4>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-amber-800">{{ pengajuan.catatan_admin || pengajuan.keterangan_admin }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Download Section -->
                    <div v-if="pengajuan.status === 'selesai' && pengajuan.file_hasil" class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Surat Selesai</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                <div class="flex items-start">
                                    <svg class="flex-shrink-0 w-8 h-8 text-green-600 mr-4 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <h5 class="text-base font-medium text-green-900">Surat Siap Diunduh</h5>
                                        <p class="text-sm text-green-700 mt-1">Dokumen surat Anda telah selesai diproses dan siap untuk diunduh.</p>
                                    </div>
                                </div>
                                <a :href="route('warga.pengajuan.download', { pengajuan: pengajuan.id })" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Unduh Surat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex items-center justify-end space-x-3">
                    <SecondaryButton @click="$emit('close')" class="px-6 py-2">
                        {{ mode === 'create' ? 'Batal' : 'Tutup' }}
                    </SecondaryButton>
                    <PrimaryButton 
                        v-if="mode === 'create'" 
                        @click="submit" 
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                        :disabled="form.processing"
                        class="px-6 py-2"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Mengajukan...' : 'Ajukan Sekarang' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>