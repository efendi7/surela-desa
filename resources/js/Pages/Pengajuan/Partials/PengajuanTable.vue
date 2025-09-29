<script setup>
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
  items: {
    type: Array,
    required: true,
  },
  emptyTitle: String,
  emptyMessage: String,
});

const { formatDate } = usePengajuanUtils();
</script>

<template>
  <div
    class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 shadow-sm"
  >
    <!-- Table View (≥640px) -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th
              class="w-3/12 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Jenis Surat
            </th>
            <th
              class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell"
            >
              Tanggal Pengajuan
            </th>
            <th
              class="w-1/12 px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              class="w-2/12 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell"
            >
              Update Terakhir
            </th>
            <th
              class="w-4/12 px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              Aksi
            </th>
          </tr>
        </thead>

        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <!-- Kosong -->
          <tr v-if="!items.length">
            <td colspan="5" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center">
                <div
                  class="w-16 h-16 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4"
                >
                  <slot name="empty-icon">
                    <svg
                      class="w-8 h-8 text-gray-400 dark:text-gray-300"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 S0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </slot>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                  {{ emptyTitle }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ emptyMessage }}
                </p>
              </div>
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-else
            v-for="item in items"
            :key="item.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
          >
            <!-- Jenis Surat -->
            <td class="px-4 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                {{ item.jenis_surat?.nama_surat || '-' }}
              </div>
            </td>

            <!-- Tanggal Pengajuan -->
            <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(item.created_at) }}
              </div>
            </td>

            <!-- Status -->
            <td class="px-4 py-4 whitespace-nowrap">
              <div class="flex justify-center">
                <StatusBadge :status="item.status" />
              </div>
            </td>

            <!-- Update Terakhir -->
            <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(item.updated_at) }}
              </div>
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
          <div
            class="w-16 h-16 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4"
          >
            <slot name="empty-icon">
              <svg
                class="w-8 h-8 text-gray-400 dark:text-gray-300"
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
            </slot>
          </div>
          <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
            {{ emptyTitle }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ emptyMessage }}</p>
        </div>
      </div>

      <div
        v-else
        v-for="item in items"
        :key="item.id"
        class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm"
      >
        <!-- Status di pojok kanan atas -->
        <div class="absolute top-4 right-4">
          <StatusBadge :status="item.status" />
        </div>

        <!-- Jenis Surat -->
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate pr-24 mb-2">
          {{ item.jenis_surat?.nama_surat || '-' }}
        </div>

        <!-- Tanggal -->
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
          Diajukan: {{ formatDate(item.created_at) }}
        </div>

        <!-- Tombol aksi -->
        <div
          class="flex items-center space-x-3 pt-2 border-t border-gray-100 dark:border-gray-700 mt-3"
        >
          <slot name="actions" :item="item"></slot>
        </div>
      </div>
    </div>
  </div>
</template>
