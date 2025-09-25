<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  show: Boolean,
  umkm: Object,
});

const emit = defineEmits(['confirm', 'close']);
</script>

<template>
  <Modal :show="show" @close="emit('close')">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900">Hapus UMKM</h2>
      <p class="mt-1 text-sm text-gray-600">
        Apakah Anda yakin ingin menghapus UMKM "<strong>{{ umkm?.nama_usaha }}</strong>"?
        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait secara permanen.
      </p>
      <div class="mt-6 flex justify-end space-x-3">
        <SecondaryButton @click="emit('close')">Batal</SecondaryButton>
        <DangerButton @click="emit('confirm')">Ya, Hapus</DangerButton>
      </div>
    </div>
  </Modal>
</template>