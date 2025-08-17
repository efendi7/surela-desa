<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { computed } from 'vue';

const props = defineProps({
    show: Boolean,
    user: Object,
    action: String, // 'freeze' or 'restore'
});

const emit = defineEmits(['close', 'confirm']);

const texts = computed(() => {
    if (props.action === 'freeze') {
        return {
            title: 'Bekukan Pengguna?',
            message: `Anda yakin ingin membekukan pengguna "${props.user?.name}"? Pengguna ini tidak akan bisa login.`,
            button: 'Ya, Bekukan',
        };
    }
    return {
        title: 'Aktifkan Pengguna?',
        message: `Anda yakin ingin mengaktifkan kembali pengguna "${props.user?.name}"?`,
        button: 'Ya, Aktifkan',
    };
});
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">{{ texts.title }}</h2>
            <p class="mt-2 text-sm text-gray-600">{{ texts.message }}</p>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')">Batal</SecondaryButton>
                <DangerButton v-if="action === 'freeze'" class="ms-3" @click="$emit('confirm')">
                    {{ texts.button }}
                </DangerButton>
                 <PrimaryButton v-else class="ms-3" @click="$emit('confirm')">
                    {{ texts.button }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>