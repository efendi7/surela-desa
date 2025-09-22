<script setup>
import { ref, computed } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3' // TAMBAH: Import Link
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PengajuanFilter from './Partials/PengajuanFilter.vue'
import PengajuanTable from './Partials/PengajuanTable.vue'
import PengajuanDetailModal from './Partials/PengajuanDetailModal.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  pengajuanList: Object,
})

// STATE
const pengajuanData = ref([...props.pengajuanList.data])
const showDetailModal = ref(false)
const selectedPengajuan = ref(null)
const fileInputRef = ref(null)
const uploadingFileForId = ref(null)

// FILTERS
const filters = ref({
  search: '',
  status: '',
  jenisSurat: '',
  sort: 'desc',
})

const jenisSuratOptions = computed(() => {
  const uniqueJenis = [
    ...new Set(pengajuanData.value.map((p) => p.jenis_surat?.nama_surat).filter(Boolean)),
  ]
  return uniqueJenis.sort()
})

// KONFIRMASI MODAL
const showConfirmModal = ref(false)
const confirmPayload = ref({})

const openConfirmModal = (payload) => {
  confirmPayload.value = payload
  showConfirmModal.value = true
}
const closeConfirmModal = () => (showConfirmModal.value = false)
const executeConfirmAction = () => {
  if (confirmPayload.value.action) confirmPayload.value.action()
  closeConfirmModal()
}

// FILTERED DATA
const filteredPengajuan = computed(() => {
  let result = [...pengajuanData.value]
  if (filters.value.search) {
    const query = filters.value.search.toLowerCase()
    result = result.filter(
      (p) =>
        p.user?.name?.toLowerCase().includes(query) ||
        p.nomor_surat?.toLowerCase().includes(query) ||
        p.jenis_surat?.nama_surat?.toLowerCase().includes(query),
    )
  }
  if (filters.value.status) {
    result = result.filter((p) => p.status === filters.value.status)
  }
  if (filters.value.jenisSurat) {
    result = result.filter((p) => p.jenis_surat?.nama_surat === filters.value.jenisSurat)
  }
  result.sort((a, b) => {
    switch (filters.value.sort) {
      case 'asc':
        return new Date(a.created_at) - new Date(b.created_at)
      case 'name_asc':
        return (a.user?.name || '').localeCompare(b.user?.name || '')
      case 'name_desc':
        return (b.user?.name || '').localeCompare(a.user?.name || '')
      default:
        return new Date(b.created_at) - new Date(a.created_at)
    }
  })
  return result
})

// HANDLERS
const handleFilterChange = (newFilters) => (filters.value = newFilters)
const handleClearFilters = () =>
  (filters.value = { search: '', status: '', jenisSurat: '', sort: 'desc' })

const openDetailModal = (pengajuan) => {
  selectedPengajuan.value = pengajuan
  showDetailModal.value = true
}
const closeModal = () => {
  showDetailModal.value = false
  selectedPengajuan.value = null
}

// UPDATE DATA LOCAL
const updateLocalData = (updatedPengajuan) => {
  const index = pengajuanData.value.findIndex((p) => p.id === updatedPengajuan.id)
  if (index !== -1) {
    pengajuanData.value[index] = { ...pengajuanData.value[index], ...updatedPengajuan }
  }
}

// PATCH
const submitPerubahan = ({ id, formData }) => {
  router.patch(route('admin.proses.update', { pengajuanSurat: id }), formData, {
    preserveScroll: true,
    onSuccess: () => {
      updateLocalData({ id, ...formData })
      closeModal()
    },
  })
}

// UPLOAD
const triggerFileUpload = (pengajuan) => {
  selectedPengajuan.value = pengajuan
  uploadingFileForId.value = pengajuan.id
  fileInputRef.value.click()
}
const handleFileSelected = (event) => {
  const file = event.target.files[0]
  if (!file || !selectedPengajuan.value) {
    event.target.value = null
    uploadingFileForId.value = null
    return
  }
  router.post(
    route('admin.proses.upload-file', { pengajuan: selectedPengajuan.value.id }),
    { file },
    {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        // reload props inertia otomatis
        router.visit(window.location.pathname, { preserveScroll: true })
        selectedPengajuan.value = null
        uploadingFileForId.value = null
      },
      onError: (errors) => {
        alert(Object.values(errors).join('\n'))
        uploadingFileForId.value = null
      },
      onFinish: () => (uploadingFileForId.value = null),
    },
  )
  event.target.value = null
}

// KONFIRMASI SELESAI
const konfirmasiSelesai = (pengajuan) => {
  openConfirmModal({
    title: 'Konfirmasi Finalisasi Surat',
    message: `Anda yakin ingin menyelesaikan pengajuan untuk ${pengajuan.user.name}?`,
    confirmText: 'Ya, Selesaikan',
    confirmColor: 'green',
    action: () => {
      router.post(
        route('admin.proses.konfirmasiFinal', { pengajuan: pengajuan.id }),
        {},
        {
          preserveScroll: true,
          onSuccess: () => {
            // reload props inertia otomatis
            router.visit(window.location.pathname, { preserveScroll: true })
          },
        },
      )
    },
  })
}

// HAPUS FILE
const hapusFile = (pengajuan) => {
  openConfirmModal({
    title: 'Konfirmasi Hapus File',
    message: `Anda yakin ingin menghapus file sementara untuk ${pengajuan.user.name}?`,
    confirmText: 'Ya, Hapus File',
    confirmColor: 'red',
    action: () => {
      router.delete(route('admin.proses.hapusFile', { pengajuan: pengajuan.id }), {
        preserveScroll: true,
        onSuccess: () => updateLocalData({ id: pengajuan.id, file_final: null }),
      })
    },
  })
}
</script>

<template>
  <Head title="Proses Pengajuan Surat" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proses Pengajuan Surat</h2>
    </template>

    <input
      type="file"
      ref="fileInputRef"
      @change="handleFileSelected"
      class="hidden"
      accept=".doc,.docx,.pdf"
    />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <FlashMessage />

            <!-- TAMBAH: Navigation Toggle -->
            <div class="mb-6 flex space-x-2">
              <Link :href="route('admin.proses.index')"
                    class="px-4 py-2 rounded-md text-sm font-medium border"
                    :class="{ 
                      'bg-blue-600 text-white border-blue-600': route().current('admin.proses.index'), 
                      'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !route().current('admin.proses.index') 
                    }">
                Pengajuan Aktif
              </Link>
              <Link :href="route('admin.proses.riwayat')"
                    class="px-4 py-2 rounded-md text-sm font-medium border"
                    :class="{ 
                      'bg-blue-600 text-white border-blue-600': route().current('admin.proses.riwayat'), 
                      'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !route().current('admin.proses.riwayat') 
                    }">
                Riwayat Pengajuan
              </Link>
            </div>

            <PengajuanFilter
              :jenis-surat-options="jenisSuratOptions"
              @filter-changed="handleFilterChange"
              @clear-filters="handleClearFilters"
            />

            <PengajuanTable
              :pengajuan="filteredPengajuan"
              :total-data="pengajuanData.length"
              :uploading-file-for-id="uploadingFileForId"
              @open-detail="openDetailModal"
              @trigger-upload="triggerFileUpload"
              @confirm-selesai="konfirmasiSelesai"
              @delete-file="hapusFile"
            />
          </div>
        </div>
      </div>
    </div>

    <PengajuanDetailModal
      :show="showDetailModal"
      :pengajuan="selectedPengajuan"
      @close="closeModal"
      @submit="submitPerubahan"
    />
    <ConfirmationModal
      :show="showConfirmModal"
      :title="confirmPayload.title"
      :message="confirmPayload.message"
      :confirm-text="confirmPayload.confirmText"
      :confirm-color="confirmPayload.confirmColor"
      @close="closeConfirmModal"
      @confirm="executeConfirmAction"
    />
  </AuthenticatedLayout>
</template>