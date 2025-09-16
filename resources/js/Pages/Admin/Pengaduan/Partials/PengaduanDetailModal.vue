<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';

// --- PROPS & EMITS ---
const props = defineProps({
    show: { type: Boolean, default: false },
    pengaduan: { type: Object, default: null },
});

const emit = defineEmits(['close']);

// --- FORMS ---
const detailsForm = useForm({
    status: '',
    prioritas: '',
    keterangan_admin: '',
    estimasi_selesai: '',
});

const photoForm = useForm({
    foto_proses: null,
});

// --- WATCHER ---
watch(() => props.pengaduan, (newVal) => {
    if (newVal) {
        detailsForm.defaults({
            status: newVal.status,
            prioritas: newVal.prioritas,
            keterangan_admin: newVal.keterangan_admin || '',
            estimasi_selesai: newVal.estimasi_selesai || '',
        });
        detailsForm.reset();
    }
}, { immediate: true, deep: true });

// --- METHODS ---
const updateDetails = () => {
    if (!props.pengaduan) return;
    detailsForm.patch(route('admin.pengaduan.updateDetails', props.pengaduan.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Modal tetap terbuka untuk melihat perubahan
        },
    });
};

const uploadProses = () => {
    if (!props.pengaduan) return;
    photoForm.post(route('admin.pengaduan.upload.proses', props.pengaduan.id), {
        preserveScroll: true,
        onSuccess: () => {
           photoForm.reset();
        },
    });
};

const handleFileChange = (event) => {
    photoForm.foto_proses = event.target.files[0];
};

const closeModal = () => emit('close');

// --- HELPERS ---
const getStatusBadgeClass = (status) => {
    const classes = {
        'Dikirim': 'bg-blue-100 text-blue-800',
        'Diterima': 'bg-yellow-100 text-yellow-800',
        'Diproses': 'bg-orange-100 text-orange-800',
        'Selesai': 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getPrioritasBadgeClass = (prioritas) => {
    const classes = {
        'Rendah': 'bg-green-100 text-green-800',
        'Sedang': 'bg-yellow-100 text-yellow-800', 
        'Tinggi': 'bg-orange-100 text-orange-800',
        'Darurat': 'bg-red-100 text-red-800'
    };
    return classes[prioritas] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <div v-if="pengaduan" class="flex flex-col max-h-[90vh] bg-gray-50 rounded-2xl overflow-hidden shadow-xl">
            <!-- Header (fix) -->
            <header class="px-6 py-4 border-b bg-white">
                <h2 class="text-lg font-medium text-gray-900">
                    Detail Pengaduan: {{ pengaduan.judul }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    ID Laporan: #{{ pengaduan.id }} | Oleh: {{ pengaduan.user.name }}
                </p>
            </header>
            
            <!-- Body (scrollable) -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Kolom Kiri - Detail Pengaduan -->
                    <div class="space-y-6">
                        <!-- Informasi Pelapor -->
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Pelapor</h3>
                            <div class="text-sm space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.user.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">NIK</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.user.nik || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kategori</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.kategori || 'Umum' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Status</span>
                                    <span :class="getStatusBadgeClass(pengaduan.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                        {{ pengaduan.status }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Prioritas</span>
                                    <span :class="getPrioritasBadgeClass(pengaduan.prioritas)" class="px-2 py-1 rounded-full text-xs font-medium">
                                        {{ pengaduan.prioritas }}
                                    </span>
                                </div>
                            </div>
                        </section>

                        <!-- Detail Pengaduan -->
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Detail Pengaduan</h3>
                            <div class="space-y-3">
                                <div>
                                    <h4 class="font-medium text-gray-600 mb-1">Lokasi</h4>
                                    <p class="text-sm text-gray-800">{{ pengaduan.alamat || 'Tidak ada detail alamat.' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-600 mb-1">Deskripsi Masalah</h4>
                                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-600 mb-1">Penanggung Jawab</h4>
                                    <p class="text-sm text-gray-800">{{ pengaduan.admin ? pengaduan.admin.name : 'Belum ditugaskan' }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Dokumentasi -->
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Dokumentasi</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Foto Bukti Warga -->
                                <div>
                                    <h4 class="font-medium text-gray-600 mb-2">Foto Bukti Warga</h4>
                                    <a :href="pengaduan.foto_bukti_url" target="_blank" class="block">
                                        <img :src="pengaduan.foto_bukti_url" class="rounded-lg w-full h-32 object-cover border hover:opacity-90 transition" alt="Foto Bukti">
                                    </a>
                                </div>
                                <!-- Foto Proses -->
                                <div v-if="pengaduan.foto_proses_url">
                                    <h4 class="font-medium text-gray-600 mb-2">Foto Bukti Penanganan</h4>
                                    <a :href="pengaduan.foto_proses_url" target="_blank" class="block">
                                        <img :src="pengaduan.foto_proses_url" class="rounded-lg w-full h-32 object-cover border hover:opacity-90 transition" alt="Foto Proses">
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Kolom Kanan - Admin Actions -->
                    <div class="space-y-6">
                        <!-- Form Update Details -->
                        <div class="p-4 bg-white rounded-lg shadow-sm space-y-4">
                            <h3 class="text-base font-semibold text-gray-800">Tindakan Admin</h3>
                            
                            <div>
                                <InputLabel for="status" value="Ubah Status" />
                                <select v-model="detailsForm.status" id="status" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Dikirim">Dikirim</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            
                            <div>
                                <InputLabel for="prioritas" value="Set Prioritas" />
                                <select v-model="detailsForm.prioritas" id="prioritas" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Rendah">Rendah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Darurat">Darurat</option>
                                </select>
                            </div>
                            
                            <div>
                                <InputLabel for="estimasi_selesai" value="Estimasi Selesai" />
                                <TextInput v-model="detailsForm.estimasi_selesai" id="estimasi_selesai" type="date" 
                                    class="mt-1 block w-full" />
                            </div>
                            
                            <div>
                                <InputLabel for="keterangan_admin" value="Keterangan Admin (Opsional)" />
                                <TextArea v-model="detailsForm.keterangan_admin" id="keterangan_admin" 
                                    class="mt-1 block w-full" rows="4" 
                                    placeholder="Berikan catatan mengenai pengaduan ini..." />
                            </div>
                        </div>

                        <!-- Form Upload Foto -->
                        <div class="p-4 bg-white rounded-lg shadow-sm space-y-4">
                            <h3 class="text-base font-semibold text-gray-800">Unggah Foto Penanganan</h3>
                            
                            <div>
                                <input type="file" @change="handleFileChange" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <div v-if="photoForm.errors.foto_proses" class="text-red-500 text-sm mt-1">
                                    {{ photoForm.errors.foto_proses }}
                                </div>
                            </div>
                            
                            <PrimaryButton @click="uploadProses" :disabled="photoForm.processing || !photoForm.foto_proses" class="w-full justify-center">
                                {{ photoForm.processing ? 'Mengunggah...' : 'Unggah Foto' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer (fix) - DIPINDAHKAN TOMBOL SIMPAN KE SINI -->
            <footer class="flex justify-between px-6 py-4 border-t bg-white">
                <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                <PrimaryButton @click="updateDetails" :disabled="detailsForm.processing" class="justify-center">
                    {{ detailsForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </PrimaryButton>
            </footer>
        </div>
    </Modal>
</template>