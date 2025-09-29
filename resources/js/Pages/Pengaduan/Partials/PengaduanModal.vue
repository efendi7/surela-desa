<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import CreatePengaduanForm from './CreatePengaduanForm.vue'
import ViewPengaduanDetail from './ViewPengaduanDetail.vue'

const props = defineProps({
  show: Boolean,
  mode: {
    type: String,
    validator: (value) => ['create', 'view'].includes(value),
    required: true,
  },
  pengaduan: Object,
})

const emit = defineEmits(['close', 'submit'])

// Form mirip create pengajuan
const form = useForm({
  judul: '',
  deskripsi: '',
  foto_bukti: null,
  alamat: '',
  kategori: '',
  latitude: '',
  longitude: '',
})

const modalTitle = computed(() =>
  props.mode === 'create' ? 'Buat Laporan / Pengaduan Baru' : 'Detail Laporan / Pengaduan'
)

watch(
  () => props.show,
  (newVal) => {
    if (newVal && props.mode === 'create') {
      form.reset()
      form.clearErrors()
    }
  }
)

const submit = () => {
  emit('submit', form)
}
</script>

<template>
  <Modal :show="show" @close="$emit('close')" max-width="4xl">
    <div class="flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 bg-white rounded-t-lg">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-900">{{ modalTitle }}</h2>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto bg-gray-50">
        <CreatePengaduanForm v-if="mode === 'create'" :form="form" />

        <ViewPengaduanDetail v-if="mode === 'view'" :pengaduan="pengaduan" />
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
        <div class="flex items-center justify-end space-x-3">
          <SecondaryButton @click="$emit('close')" class="px-6 py-2">
            {{ mode === 'create' ? 'Batal' : 'Tutup' }}
          </SecondaryButton>
          <PrimaryButton
            v-if="mode === 'create'"
            @click="submit"
            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
            :disabled="form.processing"
            class="px-6 py-2"
          >
            <svg
              v-if="form.processing"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ form.processing ? 'Mengirim...' : 'Kirim Laporan' }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </Modal>
</template>
