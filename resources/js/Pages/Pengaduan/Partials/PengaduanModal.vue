<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';

const props = defineProps({
    show: Boolean,
    mode: String, // 'create' or 'view'
    pengaduan: Object,
});

const emit = defineEmits(['close', 'submit']);

const { formatDate, getStatusClass } = usePengaduanUtils();
const photoPreview = ref(null);

const form = useForm({
    judul: '',
    deskripsi: '',
    foto_bukti: null,
    alamat: '',
    kategori: '',
    latitude: '',
    longitude: '',
});

const modalTitle = computed(() => props.mode === 'create' ? 'Buat Laporan / Pengaduan Baru' : 'Detail Laporan / Pengaduan');

// Reset form dan preview saat modal dibuka untuk mode 'create'
watch(() => props.show, (newVal) => {
    if (newVal && props.mode === 'create') {
        form.reset();
        photoPreview.value = null;
    }
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    form.foto_bukti = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    emit('submit', form);
};

// Helper functions for timeline (similar to PengajuanModal)
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

const getStatusIcon = (status) => {
    return getTimelineIcon(status);
};

// Generate timeline data from pengaduan if it doesn't exist
const timeline = computed(() => {
    if (props.pengaduan?.timeline) {
        return props.pengaduan.timeline;
    }
    
    if (!props.pengaduan) return [];
    
    // Generate basic timeline based on status and dates
    const timelineItems = [];
    
    // Always add submitted status
    timelineItems.push({
        status: 'pending',
        message: 'Pengaduan telah diterima dan menunggu verifikasi admin',
        timestamp: props.pengaduan.created_at
    });
    
    // Add processed status if exists
    if (props.pengaduan.status !== 'pending') {
        let message = '';
        switch (props.pengaduan.status) {
            case 'diproses':
                message = 'Pengaduan sedang dalam proses penanganan';
                break;
            case 'selesai':
                message = 'Pengaduan telah selesai ditangani';
                break;
            case 'ditolak':
                message = props.pengaduan.keterangan_admin || 'Pengaduan ditolak oleh admin';
                break;
        }
        
        timelineItems.push({
            status: props.pengaduan.status,
            message: message,
            timestamp: props.pengaduan.updated_at
        });
    }
    
    return timelineItems;
});
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Pengaduan</h3>
                            <p class="text-sm text-gray-600">Silakan lengkapi form di bawah ini untuk membuat laporan pengaduan.</p>
                        </div>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="judul" value="Judul Masalah" />
                                <TextInput 
                                    id="judul" 
                                    v-model="form.judul" 
                                    type="text" 
                                    class="mt-2 block w-full py-3 px-4" 
                                    placeholder="Masukkan judul masalah yang ingin dilaporkan"
                                    required 
                                />
                                <InputError class="mt-2" :message="form.errors.judul" />
                            </div>
                            
                            <div>
                                <InputLabel for="deskripsi" value="Deskripsi Lengkap Masalah" />
                                <TextArea 
                                    id="deskripsi" 
                                    v-model="form.deskripsi" 
                                    class="mt-2 block w-full py-3 px-4" 
                                    rows="5" 
                                    placeholder="Jelaskan secara detail masalah yang Anda hadapi"
                                    required 
                                />
                                <InputError class="mt-2" :message="form.errors.deskripsi" />
                            </div>
                            
                            <div>
                                <InputLabel for="kategori" value="Kategori Masalah" />
                                <TextInput 
                                    id="kategori" 
                                    v-model="form.kategori" 
                                    type="text" 
                                    class="mt-2 block w-full py-3 px-4" 
                                    placeholder="cth: Infrastruktur, Kebersihan, Keamanan" 
                                />
                                <InputError class="mt-2" :message="form.errors.kategori" />
                            </div>
                            
                            <div>
                                <InputLabel for="alamat" value="Alamat/Lokasi Kejadian" />
                                <TextArea 
                                    id="alamat" 
                                    v-model="form.alamat" 
                                    class="mt-2 block w-full py-3 px-4" 
                                    rows="3" 
                                    placeholder="Masukkan alamat atau lokasi kejadian (opsional)"
                                />
                                <InputError class="mt-2" :message="form.errors.alamat" />
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="latitude" value="Latitude (Opsional)" />
                                    <TextInput 
                                        id="latitude" 
                                        v-model="form.latitude" 
                                        type="text" 
                                        class="mt-2 block w-full py-3 px-4" 
                                        placeholder="-7.00514" 
                                    />
                                    <InputError class="mt-2" :message="form.errors.latitude" />
                                </div>
                                <div>
                                    <InputLabel for="longitude" value="Longitude (Opsional)" />
                                    <TextInput 
                                        id="longitude" 
                                        v-model="form.longitude" 
                                        type="text" 
                                        class="mt-2 block w-full py-3 px-4" 
                                        placeholder="110.43812" 
                                    />
                                    <InputError class="mt-2" :message="form.errors.longitude" />
                                </div>
                            </div>
                            
                            <div>
                                <InputLabel for="foto_bukti" value="Foto Bukti (Wajib)" />
                                <input 
                                    id="foto_bukti" 
                                    type="file" 
                                    @change="handleFileChange" 
                                    accept="image/jpeg,image/png,image/jpg" 
                                    class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer"
                                    required
                                />
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                                <InputError class="mt-2" :message="form.errors.foto_bukti" />
                                
                                <div v-if="photoPreview" class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-600 mb-3">Preview Foto:</p>
                                    <img :src="photoPreview" class="w-full h-auto max-h-64 object-contain rounded-lg shadow-sm" alt="Preview Foto Bukti">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Information -->
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <div class="flex items-start">
                            <svg class="flex-shrink-0 w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-base font-medium text-blue-900">Informasi Proses</h4>
                                <div class="mt-2 text-sm text-blue-800">
                                    <p>Setelah pengaduan disubmit, laporan akan diverifikasi oleh admin. Anda akan mendapat notifikasi tentang status pengaduan melalui email.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Mode -->
                <div v-if="mode === 'view' && pengaduan" class="p-6 space-y-6">
                    <!-- Header Info -->
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="space-y-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ pengaduan.judul }}</h3>
                                <div class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 space-y-1 sm:space-y-0 sm:space-x-4">
                                    <span>Dilaporkan pada {{ formatDate(pengaduan.created_at) }}</span>
                                    <span v-if="pengaduan.nomor_pengaduan" class="font-medium">No. {{ pengaduan.nomor_pengaduan }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" :class="pengaduan.status === 'selesai' ? 'text-green-500' : pengaduan.status === 'ditolak' ? 'text-red-500' : pengaduan.status === 'diproses' ? 'text-blue-500' : 'text-yellow-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(pengaduan.status)" />
                                </svg>
                                <span :class="getStatusBadgeClass(pengaduan.status)">
                                    {{ pengaduan.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div v-if="timeline && timeline.length" class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-6">Riwayat Status</h4>
                        <div class="flow-root">
                            <ul class="space-y-6">
                                <li v-for="(item, index) in timeline" :key="index" class="relative flex items-start space-x-4">
                                    <!-- Connector Line -->
                                    <div v-if="index !== timeline.length - 1" class="absolute top-10 left-6 w-0.5 h-full bg-gray-200 -ml-px"></div>
                                    
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

                    <!-- Pengaduan Details -->
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Pengaduan</h4>
                        <div class="space-y-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h5>
                                <p class="text-base text-gray-900 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="text-base text-gray-900">{{ pengaduan.kategori || 'Tidak ada' }}</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
                                    <dd class="text-base text-gray-900">{{ pengaduan.prioritas || 'Belum ditentukan' }}</dd>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                                    <dd class="text-base text-gray-900">{{ pengaduan.alamat || 'Tidak ada' }}</dd>
                                    <a v-if="pengaduan.latitude && pengaduan.longitude"
                                       :href="`https://www.google.com/maps/search/?api=1&query=${pengaduan.latitude},${pengaduan.longitude}`"
                                       target="_blank"
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-700 text-sm font-medium mt-1">
                                       <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                       </svg>
                                       Lihat di Google Maps
                                    </a>
                                </div>
                                <div v-if="pengaduan.estimasi_selesai" class="md:col-span-2 space-y-1">
                                    <dt class="text-sm font-medium text-gray-500">Estimasi Selesai</dt>
                                    <dd class="text-base text-gray-900">{{ formatDate(pengaduan.estimasi_selesai) }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photos Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Foto Bukti -->
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Foto Bukti dari Warga</h4>
                            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                                <a :href="pengaduan.foto_bukti_url" target="_blank" class="block w-full h-full">
                                    <img :src="pengaduan.foto_bukti_url" alt="Foto Bukti" class="w-full h-full object-cover hover:opacity-90 transition-opacity cursor-pointer">
                                </a>
                            </div>
                        </div>
                        
                        <!-- Foto Proses -->
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Foto Penanganan</h4>
                            <div v-if="pengaduan.foto_proses_url" class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                                <a :href="pengaduan.foto_proses_url" target="_blank" class="block w-full h-full">
                                    <img :src="pengaduan.foto_proses_url" alt="Foto Proses" class="w-full h-full object-cover hover:opacity-90 transition-opacity cursor-pointer">
                                </a>
                            </div>
                            <div v-else class="aspect-video bg-gray-100 rounded-lg flex flex-col items-center justify-center text-center p-6">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h5 class="font-medium text-gray-700 mb-1">Belum Ada Foto Penanganan</h5>
                                <p class="text-sm text-gray-500">Admin belum mengunggah foto proses penanganan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div v-if="pengaduan.keterangan_admin" class="bg-white rounded-lg p-6 shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Keterangan dari Admin</h4>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-amber-800 whitespace-pre-wrap">{{ pengaduan.keterangan_admin }}</p>
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
                        {{ form.processing ? 'Mengirim...' : 'Kirim Laporan' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>