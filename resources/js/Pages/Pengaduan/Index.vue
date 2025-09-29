<script setup>
import { ref, defineAsyncComponent } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import SectionHeader from '@/Components/SectionHeader.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import Tooltip from '@/Components/Tooltip.vue'

// Lazy load untuk performa
const PengaduanAktif = defineAsyncComponent(() => import('./Partials/PengaduanAktif.vue'))
const RiwayatPengaduan = defineAsyncComponent(() => import('./Partials/RiwayatPengaduan.vue'))
const PengaduanModal = defineAsyncComponent(() => import('./Partials/PengaduanModal.vue'))

const props = defineProps({
  pengaduanAktif: { type: Array, required: true },
  riwayatPengaduan: { type: Array, required: true },
  isProfileComplete: { type: Boolean, default: false },
  kategoriPengaduan: { type: Array, default: () => [] }
})

// === STATE ===
const modalState = ref({
  show: false,
  mode: 'create',
  selectedData: null
})

const confirmModalState = ref({
  show: false,
  title: '',
  message: '',
  action: null
})

const deleteForm = useForm({})

// === HANDLERS ===
function openModal (mode, data = null) {
  modalState.value = { show: true, mode, selectedData: data }
}

function closeModal () {
  modalState.value.show = false
}

function openConfirmModal ({ title, message, action }) {
  confirmModalState.value = { show: true, title, message, action }
}

function closeConfirmModal () {
  confirmModalState.value.show = false
}

function submitNewPengaduan (form) {
  form.post(route('pengaduan.store'), {
    preserveScroll: true,
    onSuccess: () => closeModal()
  })
}

function executeConfirmAction () {
  if (confirmModalState.value.action) {
    confirmModalState.value.action()
  }
  closeConfirmModal()
}

function handleConfirmAction ({ pengaduan, title, message, routeName }) {
  openConfirmModal({
    title,
    message,
    action: () => {
      deleteForm.delete(route(routeName, pengaduan.id), {
        preserveScroll: true
      })
    }
  })
}

function handleCancel (laporan) {
  handleConfirmAction({
    pengaduan: laporan,
    title: 'Batalkan Laporan',
    message: 'Apakah Anda yakin ingin membatalkan laporan ini? Laporan yang dikirim tidak dapat dikembalikan.',
    routeName: 'pengaduan.destroy'
  })
}

function handleDeleteHistory (laporan) {
  handleConfirmAction({
    pengaduan: laporan,
    title: 'Hapus Riwayat',
    message: 'Apakah Anda yakin ingin menghapus riwayat laporan ini? Tindakan ini tidak dapat dikembalikan.',
    routeName: 'pengaduan.destroy'
  })
}
</script>

<template>
  <Head>
    <title>Pengaduan Warga</title>
    <meta
      name="description"
      content="Sampaikan laporan dan pengaduan Anda secara online melalui portal desa."
    />
  </Head>

  <AuthenticatedLayout>
    <template #header>
      <h1 class="font-semibold text-xl text-gray-800 leading-tight">Laporan & Pengaduan Warga</h1>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
        <FlashMessage />

        <!-- Alert Profil Belum Lengkap -->
        <div
          v-if="!isProfileComplete"
          class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded-md"
          role="alert"
        >
          <p class="font-bold mb-1">Profil Belum Lengkap</p>
          <p class="mb-3">
            Anda harus melengkapi data profil Anda terlebih dahulu sebelum dapat membuat laporan pengaduan.
          </p>
          <Link
            :href="route('profile.edit')"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring focus:ring-red-200 active:bg-red-600 transition"
          >
            Ke Halaman Profil
          </Link>
        </div>

        <!-- Intro Section Header -->
        <div class="flex justify-center">
          <div class="max-w-3xl w-full text-center">
            <SectionHeader
              title="Sampaikan Pengaduan Anda"
              description="Laporkan masalah atau keluhan Anda kepada pemerintah desa secara online. 
                Proses lebih transparan dan dapat dipantau statusnya secara real-time."
            />
          </div>
        </div>

        <!-- Floating Action Button untuk Mobile -->
        <div class="md:hidden fixed bottom-6 right-6 z-50">
          <Tooltip text="Buat Laporan Baru">
            <button
              @click="openModal('create')"
              :disabled="!isProfileComplete"
              :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }"
              class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-105"
              aria-label="Buat Laporan Baru"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
            </button>
          </Tooltip>
        </div>

        <!-- Pengaduan Aktif -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-6">
            <div class="sm:flex sm:justify-between sm:items-start">
              <SectionHeader
                title="Laporan Sedang Diproses"
                description="Pengaduan dengan status pending atau sedang ditangani"
              >
                <template #icon>
                  <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.08 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                  </div>
                </template>
              </SectionHeader>

              <!-- Tombol Desktop - Hanya tampil di desktop -->
              <div class="hidden md:flex mt-4 justify-end sm:mt-0 sm:ml-4 flex-shrink-0">
                <PrimaryButton
                  @click="openModal('create')"
                  :disabled="!isProfileComplete"
                  :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }"
                  class="bg-red-600 hover:bg-red-700 focus:ring-red-200 active:bg-red-600"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Buat Laporan Baru
                </PrimaryButton>
              </div>
            </div>
            
            <PengaduanAktif
              :pengaduan-aktif="pengaduanAktif"
              @view-detail="(data) => openModal('view', data)"
              @cancel="handleCancel"
            />
          </div>
        </div>

        <!-- Riwayat Pengaduan -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-6">
            <SectionHeader
              title="Riwayat Laporan Anda"
              description="Pengaduan yang telah selesai diproses atau ditolak"
            >
              <template #icon>
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <svg
                    class="w-5 h-5 text-green-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                </div>
              </template>
            </SectionHeader>

            <RiwayatPengaduan
              :riwayat-pengaduan="riwayatPengaduan"
              @view-detail="(data) => openModal('view', data)"
              @delete-history="handleDeleteHistory"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <PengaduanModal
      :show="modalState.show"
      :mode="modalState.mode"
      :pengaduan="modalState.selectedData"
      :kategori-pengaduan="kategoriPengaduan"
      @close="closeModal"
      @submit="submitNewPengaduan"
    />

    <!-- Konfirmasi -->
    <ConfirmationModal
      :show="confirmModalState.show"
      :title="confirmModalState.title"
      :message="confirmModalState.message"
      confirm-color="red"
      @close="closeConfirmModal"
      @confirm="executeConfirmAction"
    />
  </AuthenticatedLayout>
</template>

<style scoped>
/* Custom styles untuk pengaduan jika diperlukan */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style>