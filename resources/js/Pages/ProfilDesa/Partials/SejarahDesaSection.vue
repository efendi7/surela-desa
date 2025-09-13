<script setup>
import { computed } from 'vue'
import { BookOpen } from 'lucide-vue-next'

const { content, pageTitle } = defineProps({
  content: {
    type: String,
    default: '',
  },
  pageTitle: {
    type: String,
    default: '',
  },
})

const isSejarahDesa = computed(() => pageTitle === 'Sejarah Desa')
</script>

<template>
  <div v-if="content" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6 sm:p-8">
      <!-- Header -->
      <div class="flex items-center gap-3 mb-6" v-if="isSejarahDesa">
        <div class="bg-amber-100 p-2 rounded-lg">
          <BookOpen class="h-6 w-6 text-amber-600" />
        </div>
        <h3 class="text-2xl font-bold text-gray-900">Sejarah Desa</h3>
      </div>

      <!-- Konten -->
      <div class="prose max-w-none">
        <div
          v-if="isSejarahDesa"
          class="border-l-4 border-amber-200 pl-6 bg-amber-50 p-4 rounded-r-lg"
        >
          <p
            class="text-gray-700 leading-relaxed whitespace-pre-line"
            v-html="content"
          ></p>
        </div>

        <!-- fallback jika bukan Sejarah Desa -->
        <div v-else class="text-gray-700 leading-relaxed whitespace-pre-line" v-html="content"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.prose :deep(h2) {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.prose :deep(ol) {
  padding-left: 1rem;
}

.prose :deep(li) {
  margin-bottom: 0.5rem;
}

.prose :deep(p) {
  margin-bottom: 1rem;
  line-height: 1.75;
  text-align: justify;
}

.prose :deep(br) {
  line-height: 1.75;
}

.prose :deep(div) {
  margin-bottom: 1rem;
}

.prose :deep(*) {
  white-space: pre-line;
}
</style>
