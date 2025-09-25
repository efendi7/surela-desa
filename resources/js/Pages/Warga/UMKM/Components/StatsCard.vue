<script setup>
defineProps({
  title: String,
  value: Number,
  icon: String,
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
    <div class="p-4">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
            </svg>
          </div>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-gray-500">{{ title }}</p>
          <p class="text-lg font-semibold text-gray-900">{{ value }}</p>
        </div>
      </div>
    </div>
  </div>
</template>