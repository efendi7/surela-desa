<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Konfirmasi Tindakan',
    },
    message: {
        type: String,
        default: 'Apakah Anda yakin ingin melakukan tindakan ini?', // Berikan default value
    },
    confirmText: {
        type: String,
        default: 'Konfirmasi',
    },
    confirmColor: {
        type: String,
        default: 'indigo', // 'indigo', 'red', 'green'
    },
});

const emit = defineEmits(['close', 'confirm']);

const colorClasses = computed(() => {
    switch (props.confirmColor) {
        case 'red':
            return 'bg-red-600 hover:bg-red-500 focus:bg-red-700 focus:ring-red-500';
        case 'green':
            return 'bg-green-600 hover:bg-green-500 focus:bg-green-700 focus:ring-green-500';
        default:
            return 'bg-indigo-600 hover:bg-indigo-500 focus:bg-indigo-700 focus:ring-indigo-500';
    }
});
</script>

<template>
    <Modal :show="show" max-width="lg" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ title }}
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')">
                    Batal
                </SecondaryButton>

                <PrimaryButton
                    @click="$emit('confirm')"
                    :class="colorClasses"
                >
                    {{ confirmText }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>