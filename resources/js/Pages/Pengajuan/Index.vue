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
const PengajuanAktif = defineAsyncComponent(() => import('./Partials/PengajuanAktif.vue'))
const RiwayatPengajuan = defineAsyncComponent(() => import('./Partials/RiwayatPengajuan.vue'))
const PengajuanModal = defineAsyncComponent(() => import('./Partials/PengajuanModal.vue'))

const props = defineProps({
  jenisSuratTersedia: { type: Array, required: true },
  pengajuanAktif: { type: Array, required: true },
  riwayatPengajuan: { type: Array, required: true },
  isProfileComplete: { type: Boolean, default: false }
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

function submitNewPengajuan (form) {
  form.post(route('pengajuan.store'), {
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

function handleConfirmAction ({ pengajuan, title, message, routeName }) {
  openConfirmModal({
    title,
    message,
    action: () => {
      deleteForm.delete(route(routeName, pengajuan.id), {
        preserveScroll: true
      })
    }
  })
}

function handleCancel (pengajuan) {
  handleConfirmAction({
    pengajuan,
    title: 'Batalkan Pengajuan',
    message:
      'Apakah Anda yakin ingin membatalkan pengajuan ini? Tindakan ini tidak dapat dikembalikan.',
    routeName: 'pengajuan.destroy'
  })
}

function handleDeleteHistory (pengajuan) {
  handleConfirmAction({
    pengajuan,
    title: 'Hapus Riwayat',
    message:
      'Apakah Anda yakin ingin menghapus riwayat pengajuan ini? Ini tidak akan bisa dikembalikan.',
    routeName: 'pengajuan.destroy-riwayat'
  })
}
</script>

<template>
  <Head>
    <title>Pengajuan Surat</title>
    <meta
      name="description"
      content="Ajukan surat resmi secara online melalui portal desa Anda."
    />
  </Head>

  <AuthenticatedLayout>
    <template #header>
      <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Pengajuan Surat Anda
      </h1>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
        <FlashMessage />

        <!-- Alert Profil Belum Lengkap -->
        <div
          v-if="!isProfileComplete"
          class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 text-yellow-800 dark:text-yellow-200 p-4 rounded-md"
          role="alert"
        >
          <p class="font-bold mb-1">Profil Belum Lengkap</p>
          <p class="mb-3">
            Anda harus melengkapi data profil Anda terlebih dahulu sebelum dapat mengajukan surat.
          </p>
          <Link
            :href="route('profile.edit')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 active:bg-blue-600 transition"
          >
            Ke Halaman Profil
          </Link>
        </div>

        <!-- Intro Section Header di atas -->
        <div class="flex justify-center">
          <div class="max-w-3xl w-full text-center">
            <SectionHeader
              title="Ajukan Surat Resmi dengan Mudah"
              description="Ajukan surat resmi Anda secara online melalui portal desa.
                Proses lebih cepat, mudah, dan transparan tanpa harus datang langsung ke kantor desa."
            />
          </div>
        </div>

        <!-- Floating Action Button untuk Mobile -->
        <div class="md:hidden fixed bottom-6 right-6 z-50">
          <Tooltip text="Ajukan Surat Baru">
            <button
              @click="openModal('create')"
              :disabled="!isProfileComplete"
              :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }"
              class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-105"
              aria-label="Ajukan Surat Baru"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
            </button>
          </Tooltip>
        </div>

        <!-- Pengajuan Aktif -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-6">
            <div class="sm:flex sm:justify-between sm:items-start">
              <SectionHeader
                title="Pengajuan Sedang Diproses"
                description="Pengajuan dengan status pending atau sedang diproses"
              >
                <template #icon>
                  <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                      <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                  </div>
                </template>
              </SectionHeader>

              <!-- Tombol Desktop -->
              <div class="hidden md:flex mt-4 justify-end sm:mt-0 sm:ml-4 flex-shrink-0">
                <PrimaryButton
                  @click="openModal('create')"
                  :disabled="!isProfileComplete"
                  :class="{ 'opacity-50 cursor-not-allowed': !isProfileComplete }"
                >
                  Ajukan Surat Baru
                </PrimaryButton>
              </div>
            </div>
            <PengajuanAktif
              :pengajuan-aktif="pengajuanAktif"
              @view-detail="(data) => openModal('view', data)"
              @cancel="handleCancel"
            />
          </div>
        </div>

        <!-- Riwayat Pengajuan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-6">
            <SectionHeader
              title="Pengajuan yang Selesai Diproses"
              description="Riwayat pengajuan dengan status selesai atau ditolak"
            >
              <template #icon>
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                  <svg
                    class="w-5 h-5 text-green-600 dark:text-green-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </div>
              </template>
            </SectionHeader>

            <RiwayatPengajuan
              :riwayat-pengajuan="riwayatPengajuan"
              @view-detail="(data) => openModal('view', data)"
              @delete-history="handleDeleteHistory"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <PengajuanModal
      :show="modalState.show"
      :mode="modalState.mode"
      :pengajuan="modalState.selectedData"
      :jenis-surat-tersedia="jenisSuratTersedia"
      @close="closeModal"
      @submit="submitNewPengajuan"
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
