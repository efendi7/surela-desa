<script setup>
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Konfirmasi Tindakan Berbahaya',
    },
    message: {
        type: String,
        default: 'Tindakan ini tidak dapat dibatalkan. Pastikan Anda yakin sebelum melanjutkan.',
    },
    confirmationWord: {
        type: String,
        default: 'HAPUS', // ✅ Berikan default value alih-alih required: true
    },
    confirmText: {
        type: String,
        default: 'Konfirmasi',
    },
});

const emit = defineEmits(['close', 'confirm']);

const userInput = ref('');

// Tombol konfirmasi akan aktif jika input pengguna cocok dengan kata konfirmasi
const isConfirmed = computed(() => userInput.value === props.confirmationWord);

// Reset input setiap kali modal dibuka
watch(() => props.show, (newValue) => {
    if (newValue) {
        userInput.value = '';
    }
});
</script>

<template>
    <Modal :show="show" max-width="lg" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-red-700">
                {{ title }}
            </h2>

            <p class="mt-2 text-sm text-gray-600" v-html="message"></p>

            <div class="mt-6">
                <InputLabel 
                    :value="`Untuk melanjutkan, ketik '${confirmationWord}' di bawah ini.`" 
                    class="font-semibold text-gray-800"
                />
                <TextInput
                    v-model="userInput"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Ketik di sini..."
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')">
                    Batal
                </SecondaryButton>

                <PrimaryButton
                    @click="$emit('confirm')"
                    class="bg-red-600 hover:bg-red-500 focus:bg-red-700 focus:ring-red-500"
                    :disabled="!isConfirmed"
                >
                    {{ confirmText }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>