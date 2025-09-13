<script setup>
import { Eye, Target } from 'lucide-vue-next'
import { computed } from 'vue'

// simpan props ke variabel
const props = defineProps({
  visi: {
    type: String,
    default: '',
  },
  misi: {
    type: Array,
    default: () => [],
  },
})

// computed untuk parse misi
const parsedMisi = computed(() => {
  return Array.isArray(props.misi) ? props.misi : (props.misi ? props.misi.split('\n') : [])
})
</script>

<template>
  <div v-if="props.visi || parsedMisi.length" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    <!-- Visi -->
    <div v-if="props.visi" class="lg:col-span-2">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 h-full">
        <div class="p-6 sm:p-8 h-full flex flex-col">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-green-100 p-2 rounded-lg">
              <Eye class="h-6 w-6 text-green-600" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900">Visi</h3>
          </div>
          <div class="flex-1">
            <blockquote class="border-l-4 border-green-200 pl-6 bg-green-50 p-4 rounded-r-lg h-full flex items-center">
              <p class="text-gray-700 italic leading-relaxed font-medium">
                {{ props.visi }}
              </p>
            </blockquote>
          </div>
        </div>
      </div>
    </div>

    <!-- Misi -->
    <div v-if="parsedMisi.length" class="lg:col-span-3">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 h-full">
        <div class="p-6 sm:p-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="bg-sky-100 p-2 rounded-lg">
              <Target class="h-6 w-6 text-sky-600" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900">Misi</h3>
          </div>

          <div class="border-l-4 border-sky-200 pl-6 bg-sky-50 p-4 rounded-r-lg">
            <ol v-if="parsedMisi.length > 0"
                class="list-decimal list-outside pl-5 space-y-3 text-gray-700 leading-relaxed">
              <li v-for="(item, index) in parsedMisi" :key="`misi-${index}`" class="font-medium">
                {{ item }}
              </li>
            </ol>
            <p v-else class="text-gray-700 leading-relaxed font-medium">Misi belum diisi.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
