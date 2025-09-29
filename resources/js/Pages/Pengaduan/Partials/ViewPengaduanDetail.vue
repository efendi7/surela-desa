<script setup>
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils'
import { useStatusStyles } from '@/Composables/useStatusStyles'

const props = defineProps({
  pengaduan: Object,
})

const { formatDate } = usePengajuanUtils()
const { getStatusStyles, getStatusBadgeClass } = useStatusStyles()

// Gunakan fungsi yang sama seperti pengajuan
const getTimelineStyles = (status) => getStatusStyles(status)
</script>

<template>
  <div v-if="pengaduan" class="p-6 space-y-6">
    <!-- Header Info -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="space-y-1">
          <h3 class="text-xl font-semibold text-gray-900">{{ pengaduan.judul }}</h3>
          <div
            class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 space-y-1 sm:space-y-0 sm:space-x-4"
          >
            <span>Dilaporkan pada {{ formatDate(pengaduan.created_at) }}</span>
            <span v-if="pengaduan.nomor_pengaduan" class="font-medium">
              No. {{ pengaduan.nomor_pengaduan }}
            </span>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <svg
            class="w-5 h-5"
            :class="getStatusStyles(pengaduan.status).textColor"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              :d="getStatusStyles(pengaduan.status).icon"
            />
          </svg>
          <span :class="getStatusBadgeClass(pengaduan.status)">
            {{ pengaduan.status }}
          </span>
        </div>
      </div>
    </div>

    <!-- Timeline - Struktur sama seperti pengajuan -->
    <div v-if="pengaduan.timeline && pengaduan.timeline.length" class="bg-white rounded-lg p-6 shadow-sm">
      <h4 class="text-lg font-medium text-gray-900 mb-6">Riwayat Status</h4>
      <div class="flow-root">
        <ul class="space-y-6">
          <li v-for="(item, index) in pengaduan.timeline" :key="index" class="relative flex items-start space-x-4">
            <div
              v-if="index !== pengaduan.timeline.length - 1"
              class="absolute top-10 left-6 w-0.5 h-full bg-gray-200 -ml-px"
            ></div>
            <div
              class="relative flex items-center justify-center w-12 h-12 rounded-full ring-4 ring-white shadow-sm"
              :class="getTimelineStyles(item.status).bgColor"
            >
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" :d="getTimelineStyles(item.status).icon" />
              </svg>
            </div>
            <div class="flex-1 min-w-0 pb-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-base font-medium text-gray-900 capitalize">{{ item.status }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ item.message }}</p>
                  <p v-if="item.admin_name" class="text-xs text-gray-500 mt-1">oleh {{ item.admin_name }}</p>
                </div>
                <span class="text-sm text-gray-500 whitespace-nowrap ml-4">
                  {{ formatDate(item.timestamp) }}
                </span>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Lampiran / Foto -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Foto Bukti -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Foto Bukti</h4>
        <div v-if="pengaduan.foto_bukti_url" class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
          <a :href="pengaduan.foto_bukti_url" target="_blank" class="block w-full h-full">
            <img
              :src="pengaduan.foto_bukti_url"
              alt="Foto Bukti"
              class="w-full h-full object-cover hover:opacity-90 transition-opacity cursor-pointer"
            />
          </a>
        </div>
        <div v-else class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
          Belum ada foto bukti
        </div>
      </div>

      <!-- Foto Penanganan -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Foto Penanganan</h4>
        <div v-if="pengaduan.foto_proses_url" class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
          <a :href="pengaduan.foto_proses_url" target="_blank" class="block w-full h-full">
            <img
              :src="pengaduan.foto_proses_url"
              alt="Foto Proses"
              class="w-full h-full object-cover hover:opacity-90 transition-opacity cursor-pointer"
            />
          </a>
        </div>
        <div v-else class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
          Belum ada foto penanganan
        </div>
      </div>
    </div>

    <!-- Detail -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Pengaduan</h4>
      <div class="space-y-4">
        <div>
          <h5 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h5>
          <p class="text-base text-gray-900 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
          <div class="space-y-1">
            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
            <dd class="text-base text-gray-900">{{ pengaduan.kategori || 'Tidak ada' }}</dd>
          </div>
          <div class="space-y-1">
            <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
            <dd class="text-base text-gray-900">{{ pengaduan.prioritas || 'Belum ditentukan' }}</dd>
          </div>
          <div class="md:col-span-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
            <dd class="text-base text-gray-900">{{ pengaduan.alamat || 'Tidak ada' }}</dd>
          </div>
          <div v-if="pengaduan.estimasi_selesai" class="md:col-span-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">Estimasi Selesai</dt>
            <dd class="text-base text-gray-900">{{ formatDate(pengaduan.estimasi_selesai) }}</dd>
          </div>
        </div>
      </div>
    </div>

    <!-- Catatan Admin -->
    <div v-if="pengaduan.keterangan_admin" class="bg-white rounded-lg p-6 shadow-sm">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Keterangan Admin</h4>
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800 whitespace-pre-wrap">
        {{ pengaduan.keterangan_admin }}
      </div>
    </div>
  </div>
</template>