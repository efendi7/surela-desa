<script setup>
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
  items: {
    type: Array,
    required: true,
  },
  emptyTitle: String,
  emptyMessage: String,
});

const { formatDate } = usePengaduanUtils();
</script>

<template>
  <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
    <!-- Table View (≥640px) -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full table-fixed divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-3/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Judul Masalah
            </th>
            <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
              Kategori
            </th>
            <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
              Tanggal Laporan
            </th>
            <th class="w-1/12 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
              Update Terakhir
            </th>
            <th class="w-2/12 px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Aksi
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          <!-- Empty State -->
          <tr v-if="!items.length">
            <td colspan="6" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center">
                <div
                  class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                >
                  <slot name="empty-icon">
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
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                      />
                    </svg>
                  </slot>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">{{ emptyTitle }}</h3>
                <p class="text-sm text-gray-500">{{ emptyMessage }}</p>
              </div>
            </td>
          </tr>

          <!-- Data Rows -->
          <tr
            v-else
            v-for="item in items"
            :key="item.id"
            class="hover:bg-gray-50 transition-colors duration-150"
          >
            <!-- Judul Masalah -->
            <td class="px-4 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900 truncate">
                {{ item.judul || '-' }}
              </div>
            </td>

            <!-- Kategori -->
            <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
              <div class="text-sm text-gray-900">{{ item.kategori || '-' }}</div>
            </td>

            <!-- Tanggal Laporan -->
            <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
              <div class="text-sm text-gray-900">{{ formatDate(item.created_at) }}</div>
            </td>

            <!-- Status -->
            <td class="px-4 py-4 whitespace-nowrap">
              <div class="flex justify-center">
                <StatusBadge :status="item.status" />
              </div>
            </td>

            <!-- Update Terakhir -->
            <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
              <div class="text-sm text-gray-900">{{ formatDate(item.updated_at) }}</div>
            </td>

            <!-- Aksi -->
            <td class="px-4 py-4 text-right">
              <div class="flex items-center justify-end space-x-3">
                <slot name="actions" :item="item"></slot>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Card View (<640px) -->
    <div class="sm:hidden space-y-4 p-4">
      <div v-if="!items.length" class="py-12 text-center">
        <div class="flex flex-col items-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <slot name="empty-icon">
              <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </slot>
          </div>
          <h3 class="text-sm font-medium text-gray-900 mb-1">{{ emptyTitle }}</h3>
          <p class="text-sm text-gray-500">{{ emptyMessage }}</p>
        </div>
      </div>
      
      <div
        v-else
        v-for="item in items"
        :key="item.id"
        class="relative bg-white border border-gray-200 rounded-lg p-4 shadow-sm"
      >
        <!-- Status di pojok kanan atas -->
        <div class="absolute top-4 right-4">
          <StatusBadge :status="item.status" />
        </div>

        <!-- Judul Masalah dengan truncate -->
        <div class="text-sm font-medium text-gray-900 truncate pr-24 mb-1">
          {{ item.judul || '-' }}
        </div>

        <!-- Kategori -->
        <div class="text-xs text-gray-600 mb-2">
          {{ item.kategori || 'Tanpa Kategori' }}
        </div>

        <!-- Tanggal -->
        <div class="text-xs text-gray-500 mb-3">
          Dilaporkan: {{ formatDate(item.created_at) }}
        </div>

        <!-- Tombol aksi horizontal di bagian bawah -->
        <div class="flex items-center space-x-3 pt-2 border-t border-gray-100 mt-3">
          <slot name="actions" :item="item"></slot>
        </div>
      </div>
    </div>
  </div>
</template>