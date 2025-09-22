<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';

// --- PROPS & EMITS ---
const props = defineProps({
    show: { type: Boolean, default: false },
    pengajuan: { type: Object, default: null },
});
const emit = defineEmits(['close', 'submit']);

// --- FORM ---
const form = useForm({
    status: '',
    keterangan_admin: '',
    nomor_surat: '',
});

// --- WATCHER ---
watch(() => props.pengajuan, (newVal) => {
    if (newVal) {
        // Reset form dengan data fresh
        form.clearErrors();
        form.reset();
        
        // Set values dengan timeout untuk memastikan form sudah reset
        setTimeout(() => {
            form.status = newVal.status || '';
            form.keterangan_admin = newVal.keterangan_admin || '';
            form.nomor_surat = newVal.nomor_surat || '';
        }, 50);
    }
}, { immediate: true, deep: true });

// Reset form saat modal ditutup
watch(() => props.show, (isVisible) => {
    if (!isVisible) {
        form.reset();
        form.clearErrors();
    }
});

// --- METHODS ---
const submit = () => {
    if (props.pengajuan && !form.processing) {
        emit('submit', { 
            id: props.pengajuan.id, 
            formData: {
                status: form.status,
                keterangan_admin: form.keterangan_admin,
                nomor_surat: form.nomor_surat
            }
        });
    }
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

// --- HELPERS ---
/**
 * FUNGSI YANG DIPERBARUI
 * Bisa menangani data format baru (objek) dan lama (string).
 */
const formatFileName = (fileData) => {
    // Prioritaskan nama file asli dari format baru
    if (typeof fileData === 'object' && fileData !== null && fileData.original_name) {
        return fileData.original_name;
    }
    // Fallback jika original_name tidak ada tapi formatnya baru
    if (typeof fileData === 'object' && fileData !== null && fileData.path) {
        return fileData.path.split('/').pop();
    }
    // Handle format data lama (string)
    if (typeof fileData === 'string') {
        return fileData.split('/').pop();
    }
    return 'Nama File Tidak Tersedia';
};

const formatSyaratName = (key) => key?.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) || '';
const hasLampiran = (lampiran) => lampiran && Object.keys(lampiran).length > 0;
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <div v-if="pengajuan" class="flex flex-col max-h-[90vh] bg-gray-50 rounded-2xl overflow-hidden shadow-xl">
            <!-- Header (fix) -->
            <header class="px-6 py-4 border-b bg-white">
                <h2 class="text-lg font-medium text-gray-900">
                    Detail Pengajuan: {{ pengajuan.jenis_surat.nama_surat }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Oleh: {{ pengajuan.user.name }}
                </p>
            </header>
            
            <!-- Body (scrollable) -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Kolom Kiri -->
                    <div class="space-y-6">
                        <!-- Informasi Pemohon -->
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Pemohon</h3>
                            <div class="text-sm space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama</span>
                                    <span class="font-medium text-gray-800">{{ pengajuan.user.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">NIK</span>
                                    <span class="font-medium text-gray-800">{{ pengajuan.user.nik || '-' }}</span>
                                </div>
                            </div>
                        </section>

                        <!-- Bagian Lampiran di Modal Admin -->
                        <section class="p-4 bg-white rounded-lg shadow-sm">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Lampiran Warga</h3>
                            <div v-if="hasLampiran(pengajuan.lampiran)" class="space-y-3">
                                <a v-for="(fileData, key) in pengajuan.lampiran"
                                   :key="key"
                                   :href="route('pengajuan.lampiran.view', { pengajuan: pengajuan.id, key: key })"
                                   target="_blank"
                                   class="flex items-center p-3 border rounded-lg hover:bg-gray-50 transition">
                                    
                                    <!-- Icon -->
                                    <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 
                                              105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 
                                              005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    
                                    <!-- File Info -->
                                    <div class="ml-3 flex-grow overflow-hidden">
                                        <div class="text-sm font-medium text-gray-700">{{ formatSyaratName(key) }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ formatFileName(fileData) }}</div>
                                    </div>
                                    <span class="ml-4 text-xs font-semibold text-blue-600">Lihat</span>
                                </a>
                            </div>
                            <div v-else class="text-center p-4 border-2 border-dashed rounded-lg text-sm text-gray-500">
                                Tidak ada lampiran.
                            </div>
                        </section>
                    </div>

                    <!-- Kolom Kanan (Admin Form) -->
                    <form @submit.prevent="submit" class="h-full flex flex-col space-y-6 p-4 bg-white rounded-lg shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800">Tindakan Admin</h3>
                        

                        
                        <div>
                            <InputLabel for="nomor_surat" value="Nomor Surat" />
                            <input 
                                v-model="form.nomor_surat" 
                                id="nomor_surat" 
                                type="text"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 
                                       rounded-md shadow-sm"
                                placeholder="cth: 005/DS/I/2025" 
                            />
                            <div v-if="form.errors.nomor_surat" class="text-red-600 text-sm mt-1">
                                {{ form.errors.nomor_surat }}
                            </div>
                        </div>

                        <div>
                            <InputLabel for="status" value="Ubah Status" />
                            <select 
                                v-model="form.status" 
                                id="status"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 
                                       rounded-md shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="selesai" disabled>Selesai (via Konfirmasi)</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <div class="flex-1">
                            <InputLabel for="keterangan_admin" value="Keterangan Admin (Opsional)" />
                            <textarea 
                                v-model="form.keterangan_admin" 
                                id="keterangan_admin" 
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 
                                       rounded-md shadow-sm"
                                placeholder="Berikan catatan jika pengajuan ditolak...">
                            </textarea>
                            <div v-if="form.errors.keterangan_admin" class="text-red-600 text-sm mt-1">
                                {{ form.errors.keterangan_admin }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer (fix) -->
            <footer class="flex justify-end space-x-3 px-6 py-4 border-t bg-white">
                <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                <PrimaryButton @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </PrimaryButton>
            </footer>
        </div>
    </Modal>
</template>