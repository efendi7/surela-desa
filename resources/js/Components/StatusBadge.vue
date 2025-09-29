<script setup>
import { useStatusStyles } from '@/Composables/useStatusStyles';

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  position: {
    type: String,
    default: 'inline',
  },
});

const { getStatusStyles } = useStatusStyles();
const styles = getStatusStyles(props.status);
</script>

<template>
  <div
    :class="[
      position === 'absolute'
        ? 'absolute top-2 right-2'
        : 'flex items-center',
    ]"
  >
    <!-- Icon -->
    <div v-if="position === 'inline'" class="flex-shrink-0 mr-2">
      <svg
        class="w-4 h-4"
        :class="[styles.textColor, { 'animate-spin': status === 'diproses' }]"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          :d="styles.icon"
        />
      </svg>
    </div>

    <!-- Badge -->
    <span
      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
             border"
      :class="[
        styles.badgeClass, // warna status (tetap hijau, kuning, merah, dll)
        styles.textColor,  // warna ikon
        'border-current'   // biar border ikut warna text badge
      ]"
    >
      {{ status.replace('_', ' ') }}
    </span>
  </div>
</template>
