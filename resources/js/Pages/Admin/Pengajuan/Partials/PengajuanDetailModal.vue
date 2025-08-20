<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    pengajuan: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'submit']);

const form = useForm({
    status: '',
    keterangan_admin: '',
    nomor_surat: '',
});

watch(() => props.pengajuan, (newVal) => {
    if (newVal) {
        form.defaults({
            status: newVal.status,
            keterangan_admin: newVal.keterangan_admin || '',
            nomor_surat: newVal.nomor_surat || '',
        });
        form.reset();
    }
});

const submit = () => {
    if (props.pengajuan) {
        emit('submit', { id: props.pengajuan.id, formData: form });
    }
};

const closeModal = () => {
    emit('close');
};

// DIPERBAIKI: Fungsi untuk mengambil nama file dari path lengkap
const formatFileName = (filePath) => {
    if (!filePath) return '';
    return filePath.split('/').pop();
};

// BARU: Fungsi untuk mengubah key syarat menjadi nama yang readable
const formatSyaratName = (syaratKey) => {
    if (!syaratKey) return '';
    return syaratKey.replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
};

// BARU: Fungsi untuk mengecek apakah lampiran adalah object atau tidak
const hasLampiran = (lampiran) => {
    return lampiran && typeof lampiran === 'object' && Object.keys(lampiran).length > 0;
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <div class="flex flex-col max-h-[90vh]" v-if="pengajuan">
            <!-- Header - Fixed -->
            <div class="px-6 py-4 border-b bg-white">
                <h2 class="text-lg font-medium text-gray-900">
                    Detail: {{ pengajuan.jenis_surat.nama_surat }}
                </h2>
            </div>
            
            <!-- Content - Scrollable -->
            <div class="flex-1 overflow-y-auto">
                <form @submit.prevent="submit" class="p-6 space-y-6">
    
                    <div class="mb-4 bg-gray-50 p-3 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><strong>Nama Pemohon:</strong> {{ pengajuan.user.name }}</div>
                            <div><strong>NIK:</strong> {{ pengajuan.user.nik || '-' }}</div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-800 mb-3 border-b pb-2">
                            Lampiran dari Warga
                        </h3>
                        
                        <!-- DIPERBAIKI: Kondisi dan loop untuk menampilkan lampiran -->
                        <div v-if="hasLampiran(pengajuan.lampiran)" class="space-y-3">
                            <div 
                                v-for="(filePath, syaratKey) in pengajuan.lampiran" 
                                :key="syaratKey"
                                class="flex items-center p-3 border border-gray-200 rounded-md bg-gray-50 hover:bg-gray-100 transition-colors"
                            >
                                <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" />
                                </svg>
                                
                                <div class="ml-3 flex-grow">
                                    <div class="text-sm font-medium text-gray-700">
                                        {{ formatSyaratName(syaratKey) }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ formatFileName(filePath) }}
                                    </div>
                                </div>

                                <a 
                                    :href="`/storage/${filePath}`" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="ml-4 flex-shrink-0 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200"
                                >
                                    Unduh
                                </a>
                            </div>
                        </div>
                        <div v-else class="flex items-center p-4 border border-dashed border-gray-300 rounded-lg bg-gray-50">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-500">
                                Tidak ada lampiran yang diunggah oleh warga.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <InputLabel for="nomor_surat" value="Nomor Surat" />
                            <input
                                v-model="form.nomor_surat"
                                id="nomor_surat"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="cth: 005/DS/I/2025"
                            />
                        </div>

                        <div>
                            <InputLabel for="status" value="Ubah Status" />
                            <select
                                v-model="form.status"
                                id="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai" disabled>
                                    Selesai (Otomatis via Konfirmasi)
                                </option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="keterangan_admin" value="Keterangan Admin (Opsional)" />
                            <textarea
                                v-model="form.keterangan_admin"
                                id="keterangan_admin"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Berikan keterangan atau catatan untuk pengajuan ini..."
                            ></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer - Fixed -->
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <SecondaryButton type="button" @click="closeModal">Tutup</SecondaryButton>
                <PrimaryButton 
                    type="button" 
                    @click="submit"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Perubahan</span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>