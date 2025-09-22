<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils'
import ProcessDropdown from '@/Components/ProcessDropdown.vue'
import UploadProgressModal from '@/Components/UploadProgressModal.vue'

// PROPS
const props = defineProps({
  pengajuan: {
    type: Array,
    required: true,
  },
  isRiwayat: {
    type: Boolean,
    default: false,
  },
})

// EMITS
const emit = defineEmits([
  'open-detail',
  'confirm-selesai',
  'delete-file',
  'delete-riwayat',
])

// COMPOSABLES
const { getStatusClass, formatDate } = usePengajuanUtils()

// PAGINATION STATE
const currentPage = ref(1)
const perPage = 10
const totalPages = computed(() => Math.ceil(props.pengajuan.length / perPage))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return props.pengajuan.slice(start, start + perPage)
})
const changePage = (page) => (currentPage.value = page)

// UPLOAD MODAL
const isModalVisible = ref(false)
const uploadProgress = ref(0)
const uploadingFileName = ref('')
const currentItemForUpload = ref(null)
const fileInput = ref(null)

// Trigger file input
const handleTriggerUpload = (item) => {
  currentItemForUpload.value = item
  fileInput.value.click()
}

// Setelah file dipilih
const handleFileSelected = (event) => {
  const file = event.target.files[0]
  if (!file || !currentItemForUpload.value) return
  uploadFile(file, currentItemForUpload.value.id)
  event.target.value = null
}

// Upload file ke server
const uploadFile = (file, itemId) => {
  uploadingFileName.value = file.name
  uploadProgress.value = 0
  isModalVisible.value = true

  router.post(
    route('admin.proses.upload-file', { pengajuan: itemId }),
    { file_final: file },
    {
      forceFormData: true,
      preserveScroll: true,
      onProgress: (progress) => {
        if (progress.lengthComputable) {
          uploadProgress.value = Math.round((progress.loaded / progress.total) * 100)
        }
      },
      onSuccess: () => {
        // ✅ reload props inertia setelah upload berhasil
        router.visit(window.location.pathname, { preserveScroll: true })
      },
      onError: (errors) => {
        console.error('File upload failed:', errors)
      },
      onFinish: () => {
        setTimeout(() => (isModalVisible.value = false), 500)
      },
    },
  )
}
</script>

<template>
  <div>
    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
      <div class="table-container">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Pemohon
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Jenis Surat
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tanggal Pengajuan
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Update Terakhir
              </th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ isRiwayat ? 'Aksi' : 'Proses Surat' }}
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="pengajuan.length === 0">
              <td colspan="6" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center">
                  <div
                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                  >
                    <svg
                      class="w-8 h-8 text-gray-400"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </div>
                  <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pengajuan</h3>
                  <p class="text-sm text-gray-500">
                    Belum ada data yang sesuai dengan filter yang dipilih
                  </p>
                </div>
              </td>
            </tr>

            <tr
              v-else
              v-for="item in paginatedData"
              :key="item.id"
              class="hover:bg-gray-50 transition-colors duration-150"
            >
              <td class="px-4 py-4">
                <div class="flex items-center">
                  <div
                    class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center mr-3"
                  >
                    <img
                      v-if="item.user?.profile_photo_url"
                      :src="item.user.profile_photo_url"
                      alt="Foto Pemohon"
                      class="w-full h-full object-cover"
                    />
                    <span v-else class="text-xs font-medium text-gray-700">{{
                      item.user?.name?.charAt(0)?.toUpperCase() || '?'
                    }}</span>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ item.user?.name || 'Nama tidak tersedia' }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ item.user?.nik || 'NIK tidak tersedia' }}
                    </div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.jenis_surat?.nama_surat || '-' }}
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="text-sm text-gray-900">{{ formatDate(item.created_at) }}</div>
              </td>

              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize"
                  :class="getStatusClass(item.status)"
                >
                  {{ item.status.replace('_', ' ') }}
                </span>
              </td>

              <td class="px-4 py-4">
                <div class="text-sm text-gray-900">{{ formatDate(item.updated_at) }}</div>
              </td>

              <td class="px-4 py-4 text-right">
                <div v-if="isRiwayat" class="flex items-center justify-end space-x-3">
                  <button
                    @click="emit('open-detail', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100"
                  >
                    Detail
                  </button>
                  <a
                    v-if="item.status === 'selesai' && item.file_hasil"
                    :href="`/storage/${item.file_hasil}`"
                    download
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Unduh
                  </a>
                  <button
                    @click="emit('delete-riwayat', item)"
                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100"
                  >
                    Hapus
                  </button>
                </div>

                <ProcessDropdown
                  v-else
                  :item="item"
                  @open-detail="emit('open-detail', $event)"
                  @trigger-upload="handleTriggerUpload"
                  @confirm-selesai="emit('confirm-selesai', $event)"
                  @delete-file="emit('delete-file', $event)"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total-items="pengajuan.length"
        @page-changed="changePage"
      />
    </div>

    <!-- Hidden input file untuk upload -->
    <input
      type="file"
      ref="fileInput"
      @change="handleFileSelected"
      class="hidden"
      accept=".pdf,.doc,.docx"
    />

    <!-- Modal Progress Upload -->
    <UploadProgressModal
      :show="isModalVisible"
      :file-name="uploadingFileName"
      :progress="uploadProgress"
    />
  </div>
</template>

<style scoped>
.table-container {
  overflow-x: auto;
}
</style>