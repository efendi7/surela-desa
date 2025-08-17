<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    surat: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'edit']);

const closeModal = () => {
    emit('close');
};

const editSurat = () => {
    emit('edit', props.surat);
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="2xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Detail Jenis Surat</h2>
            <p class="mt-1 text-sm text-gray-600">
                Lihat detail surat. Klik edit untuk mengubah.
            </p>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4 flex-1 overflow-y-auto">
            <div class="space-y-4">
                <!-- Nama Surat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Surat
                    </label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <p class="text-gray-900">{{ surat.nama_surat || '-' }}</p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <p class="text-gray-900">{{ surat.deskripsi || '-' }}</p>
                    </div>
                </div>

                <!-- Syarat-syarat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Syarat-syarat
                    </label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <div v-if="surat.syarat && surat.syarat.length > 0" class="flex flex-wrap gap-2">
                            <span 
                                v-for="syarat in surat.syarat" 
                                :key="syarat" 
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                                {{ syarat }}
                            </span>
                        </div>
                        <p v-else class="text-gray-500">Tidak ada syarat yang ditetapkan</p>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div v-if="surat.created_at || surat.updated_at" class="pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div v-if="surat.created_at">
                            <span class="font-medium">Dibuat:</span>
                            <p>{{ new Date(surat.created_at).toLocaleDateString('id-ID', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) }}</p>
                        </div>
                        <div v-if="surat.updated_at && surat.updated_at !== surat.created_at">
                            <span class="font-medium">Terakhir diubah:</span>
                            <p>{{ new Date(surat.updated_at).toLocaleDateString('id-ID', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-2">
            <SecondaryButton @click="closeModal">
                Tutup
            </SecondaryButton>
            <PrimaryButton @click="editSurat">
                Edit
            </PrimaryButton>
        </div>
    </Modal>
</template>