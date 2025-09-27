<script setup>
defineProps({
  title: String,
  value: Number,
  icon: String,
  color: { type: String, default: 'blue' },
});

const colorClasses = {
  blue: 'bg-blue-100 text-blue-600',
  green: 'bg-green-100 text-green-600',
  purple: 'bg-purple-100 text-purple-600',
  orange: 'bg-orange-100 text-orange-600',
};
</script>

<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-lg border hover:shadow-md transition-shadow">
    <div class="p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="colorClasses[color] || colorClasses.blue">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
            </svg>
          </div>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-500">{{ title }}</p>
          <p class="text-2xl font-bold text-gray-900">{{ value?.toLocaleString() || 0 }}</p>
        </div>
      </div>
    </div>
  </div>
</template>