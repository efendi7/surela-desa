<template>
    <Modal :show="show" @close="closeModal" max-width="md">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <!-- Warning Icon -->
                <div class="flex-shrink-0 w-10 h-10 mx-auto flex items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Body (scrollable) -->
        <div class="px-6 py-4 max-h-96 overflow-y-auto">
            <div class="space-y-4">
                <!-- Warning Message -->
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Peringatan!</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Anda akan menghapus jenis surat <strong>"{{ surat?.nama_surat }}"</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Informasi (awal tertutup) -->
                <details class="bg-gray-50 rounded-lg p-4">
                    <summary class="cursor-pointer text-sm font-medium text-gray-900 mb-2">
                        Detail yang akan dihapus
                    </summary>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2 mt-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Surat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ surat.nama_surat || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ surat.deskripsi || '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Syarat-syarat</dt>
                            <dd class="mt-1">
                                <div v-if="surat.syarat && surat.syarat.length > 0" class="flex flex-wrap gap-1">
                                    <span 
                                        v-for="syarat in surat.syarat" 
                                        :key="syarat" 
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        {{ syarat }}
                                    </span>
                                </div>
                                <span v-else class="text-sm text-gray-500 italic">Tidak ada syarat</span>
                            </dd>
                        </div>
                    </dl>
                </details>

               <!-- Konsekuensi Penghapusan -->
<div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
    <div class="flex items-start">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <!-- Text -->
        <div class="ml-3">
            <h3 class="text-sm font-semibold text-yellow-800">
                Dampak Penghapusan
            </h3>
            <ul class="mt-2 text-sm text-yellow-700 space-y-2 list-disc list-inside">
                <li>Data jenis surat akan dihapus secara permanen</li>
                <li>Pengajuan surat yang menggunakan jenis ini mungkin terpengaruh</li>
                <li>Riwayat terkait jenis surat ini akan hilang</li>
            </ul>
        </div>
    </div>
</div>


                <!-- Konfirmasi Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ketik <span class="font-semibold text-red-600">HAPUS</span> untuk mengonfirmasi:
                    </label>
                    <input 
                        v-model="confirmationText"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500"
                        :class="{
                            'border-red-300 bg-red-50': confirmationText && !isConfirmationValid,
                            'border-green-300 bg-green-50': isConfirmationValid
                        }"
                        placeholder="Ketik HAPUS"
                        autocomplete="off"
                    />
                    <p v-if="confirmationText && !isConfirmationValid" class="mt-1 text-sm text-red-600">
                        Ketik "HAPUS" dengan huruf kapital untuk mengonfirmasi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
            <SecondaryButton 
                @click="closeModal" 
                :disabled="isDeleting"
                class="focus:ring-gray-500"
            >
                <svg v-if="!isDeleting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
            </SecondaryButton>
            
            <button
                type="button"
                @click="deleteSurat"
                :disabled="!isConfirmationValid || isDeleting"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition-all duration-200"
                :class="{
                    'bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500': isConfirmationValid && !isDeleting,
                    'bg-gray-400 cursor-not-allowed': !isConfirmationValid || isDeleting
                }"
            >
                <!-- Loading Spinner -->
                <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <!-- Delete Icon -->
                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                
                {{ isDeleting ? 'Menghapus...' : 'Hapus Sekarang' }}
            </button>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: Boolean,
    surat: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

// State
const confirmationText = ref('');
const isDeleting = ref(false);

// Computed
const isConfirmationValid = computed(() => {
    return confirmationText.value === 'HAPUS';
});

// Watch untuk reset form ketika modal dibuka/ditutup
watch(() => props.show, (newVal) => {
    if (newVal) {
        confirmationText.value = '';
        isDeleting.value = false;
    }
});

// Methods
const closeModal = () => {
    if (isDeleting.value) return; // Prevent close during deletion
    
    confirmationText.value = '';
    isDeleting.value = false;
    emit('close');
};

const deleteSurat = () => {
    if (!isConfirmationValid.value || !props.surat?.id || isDeleting.value) {
        return;
    }

    isDeleting.value = true;

    router.delete(route('admin.jenis-surat.destroy', props.surat.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
        onError: (errors) => {
            console.error('Error deleting surat:', errors);
            isDeleting.value = false;
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

// Keyboard shortcuts
const handleKeyDown = (event) => {
    if (event.key === 'Enter' && isConfirmationValid.value && !isDeleting.value) {
        deleteSurat();
    }
    if (event.key === 'Escape' && !isDeleting.value) {
        closeModal();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped>
/* Custom animations */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}

/* Focus styles */
.focus-visible {
    outline: 2px solid #ef4444;
    outline-offset: 2px;
}

/* Transition untuk smooth state changes */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
