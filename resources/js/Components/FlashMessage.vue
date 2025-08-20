<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Mengambil data flash dari Inertia
const page = usePage();

// State untuk mengontrol visibilitas dan konten notifikasi
const show = ref(false);
const message = ref('');
const type = ref('success'); // 'success' atau 'error'

// Variabel untuk menyimpan instance timer
let timer = null;

// Fungsi untuk menutup notifikasi
const close = () => {
  show.value = false;
  // Membersihkan properti flash agar tidak muncul lagi saat navigasi back/forward
  page.props.flash.success = null;
  page.props.flash.error = null;
};

// Mengawasi perubahan pada properti flash
watch(
  () => [page.props.flash.success, page.props.flash.error],
  ([newSuccess, newError]) => {
    // Hentikan timer sebelumnya jika ada notifikasi baru masuk
    clearTimeout(timer);

    if (newSuccess) {
      message.value = newSuccess;
      type.value = 'success';
      show.value = true;
    } else if (newError) {
      message.value = newError;
      type.value = 'error';
      show.value = true;
    }

    // Jika notifikasi ditampilkan, set timer untuk menghilangkannya
    if (show.value) {
      timer = setTimeout(() => {
        close();
      }, 3000); // 3000 ms = 3 detik
    }
  },
  { deep: true } // Opsi untuk mendeteksi perubahan dalam objek
);

// Computed property untuk menentukan kelas CSS berdasarkan tipe notifikasi
const typeClasses = {
  success: 'bg-green-100 border-green-400 text-green-700',
  error: 'bg-red-100 border-red-400 text-red-700',
};
</script>

<template>
  <Transition name="fade">
    <div
      v-if="show"
      :class="['fixed top-5 right-5 z-50 max-w-sm w-full p-4 border rounded-lg shadow-lg flex items-center justify-between', typeClasses[type]]"
      role="alert"
    >
      <span class="font-medium">{{ message }}</span>

      <button
        @click="close"
        type="button"
        :class="['ml-4 -mr-1 p-1 rounded-md focus:outline-none focus:ring-2', typeClasses[type].replace('bg-', 'hover:bg-').replace('-100', '-200')]"
        aria-label="Close"
      >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>