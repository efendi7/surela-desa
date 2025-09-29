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

const emit = defineEmits(['close', 'updated']);

// --- GABUNGKAN FORM MENJADI SATU ---
const form = useForm({
    status: '',
    prioritas: '',
    keterangan_admin: '',
    estimasi_selesai: '',
    foto_proses: null,
    _method: 'patch',
});

// --- WATCHER ---
watch(() => props.pengaduan, (newVal) => {
    if (newVal) {
        form.defaults({
            status: newVal.status,
            prioritas: newVal.prioritas,
            keterangan_admin: newVal.keterangan_admin || '',
            estimasi_selesai: newVal.estimasi_selesai || '',
        });
        form.reset();
        form.clearErrors();
    }
}, { immediate: true, deep: true });

// --- BUAT SATU METHOD SUBMIT ---
const submitForm = () => {
    if (!props.pengaduan) return;

    form.post(route('admin.pengaduan.updateDetails', props.pengaduan.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            const fileInput = document.getElementById('foto_proses_input');
            if (fileInput) {
                fileInput.value = '';
            }
            form.foto_proses = null;
            
            const updatedData = page.props.flash.updatedPengaduan;
            if (updatedData) {
                emit('updated', updatedData);
            }
            emit('close'); 
        },
    });
};

const closeModal = () => emit('close');

// --- HELPERS - Ubah ke lowercase ---
const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-blue-100 text-blue-800',
        'diproses': 'bg-orange-100 text-orange-800',
        'selesai': 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getPrioritasBadgeClass = (prioritas) => {
    const classes = {
        'rendah': 'bg-green-100 text-green-800',
        'sedang': 'bg-yellow-100 text-yellow-800',
        'tinggi': 'bg-orange-100 text-orange-800',
        'darurat': 'bg-red-100 text-red-800'
    };
    return classes[prioritas] || 'bg-gray-100 text-gray-800';
};

// Helper untuk display text yang capitalize
const getStatusDisplayText = (status) => {
    const displays = {
        'pending': 'Pending',
        'diproses': 'Diproses', 
        'selesai': 'Selesai'
    };
    return displays[status] || status;
};

const getPrioritasDisplayText = (prioritas) => {
    const displays = {
        'rendah': 'Rendah',
        'sedang': 'Sedang',
        'tinggi': 'Tinggi',
        'darurat': 'Darurat'
    };
    return displays[prioritas] || prioritas;
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <div v-if="pengaduan" class="flex flex-col max-h-[90vh] bg-gray-50 rounded-lg overflow-hidden shadow-xl">
            <header class="px-6 py-4 border-b bg-white">
                <h2 class="text-lg font-medium text-gray-900">
                    Detail Pengaduan: {{ pengaduan.judul }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    ID Laporan: #{{ pengaduan.id }} | Oleh: {{ pengaduan.user.name }}
                </p>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <div class="space-y-6">
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Pelapor</h3>
                            <div class="text-sm divide-y divide-gray-200">
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-500">Nama</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.user.name }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-500">NIK</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.user.nik || '-' }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-500">Kategori</span>
                                    <span class="font-medium text-gray-800">{{ pengaduan.kategori || 'Umum' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500">Status</span>
                                    <span :class="getStatusBadgeClass(pengaduan.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                        {{ getStatusDisplayText(pengaduan.status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500">Prioritas</span>
                                    <span :class="getPrioritasBadgeClass(pengaduan.prioritas)" class="px-2 py-1 rounded-full text-xs font-medium">
                                        {{ getPrioritasDisplayText(pengaduan.prioritas) }}
                                    </span>
                                </div>
                            </div>
                        </section>

                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Detail Pengaduan</h3>
                            <div class="space-y-4 divide-y divide-gray-200">
                                <div class="pt-3">
                                    <h4 class="font-medium text-gray-600 text-sm mb-1">Lokasi</h4>
                                    <p class="text-sm text-gray-800">{{ pengaduan.alamat || 'Tidak ada detail alamat.' }}</p>
                                </div>
                                <div class="pt-3">
                                    <h4 class="font-medium text-gray-600 text-sm mb-1">Deskripsi Masalah</h4>
                                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
                                </div>
                                <div class="pt-3">
                                    <h4 class="font-medium text-gray-600 text-sm mb-1">Penanggung Jawab</h4>
                                    <p class="text-sm text-gray-800">{{ pengaduan.admin ? pengaduan.admin.name : 'Belum ditugaskan' }}</p>
                                </div>
                            </div>
                        </section>

                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Dokumentasi</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-600 mb-2">Foto Bukti Warga</h4>
                                    <div v-if="pengaduan.foto_bukti_url" class="aspect-video bg-gray-100 rounded-lg overflow-hidden border">
                                        <a :href="pengaduan.foto_bukti_url" target="_blank">
                                            <img :src="pengaduan.foto_bukti_url" alt="Foto Bukti" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                        </a>
                                    </div>
                                    <div v-else class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center text-sm text-gray-500 border-2 border-dashed">
                                        Tidak ada foto bukti.
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-600 mb-2">Foto Penanganan (Admin)</h4>
                                    <div v-if="pengaduan.foto_proses_url" class="aspect-video bg-gray-100 rounded-lg overflow-hidden border">
                                         <a :href="pengaduan.foto_proses_url" target="_blank">
                                            <img :src="pengaduan.foto_proses_url" alt="Foto Proses" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                        </a>
                                    </div>
                                    <div v-else class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center text-sm text-gray-500 border-2 border-dashed">
                                        Belum ada foto penanganan.
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow-sm space-y-4 sticky top-0">
                        <h3 class="text-base font-semibold text-gray-800">Tindakan Admin</h3>
                        <div>
                            <InputLabel for="status" value="Ubah Status" />
                            <select v-model="form.status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="prioritas" value="Set Prioritas" />
                            <select v-model="form.prioritas" id="prioritas" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="rendah">Rendah</option>
                                <option value="sedang">Sedang</option>
                                <option value="tinggi">Tinggi</option>
                                <option value="darurat">Darurat</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="estimasi_selesai" value="Estimasi Selesai" />
                            <TextInput v-model="form.estimasi_selesai" id="estimasi_selesai" type="date" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="foto_proses_input" value="Unggah/Ganti Foto Penanganan" />
                             <input type="file" @input="form.foto_proses = $event.target.files[0]" id="foto_proses_input" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2 h-2 rounded-full overflow-hidden"></progress>
                            <div v-if="form.errors.foto_proses" class="text-red-500 text-sm mt-1">
                                {{ form.errors.foto_proses }}
                            </div>
                        </div>
                        <div>
                            <InputLabel for="keterangan_admin" value="Keterangan Admin (Opsional)" />
                            <TextArea v-model="form.keterangan_admin" id="keterangan_admin" class="mt-1 block w-full" rows="4" placeholder="Berikan catatan mengenai pengaduan ini..." />
                        </div>
                    </div>
                </div>
            </div>

            <footer class="flex justify-between items-center px-6 py-4 border-t bg-white">
                <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                <PrimaryButton @click="submitForm" :disabled="form.processing" class="justify-center">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </PrimaryButton>
            </footer>
        </div>
    </Modal>
</template>