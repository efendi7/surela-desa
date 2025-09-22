<script setup>
import { ref, onMounted } from 'vue'

const darkMode = ref(false)

// saat mount, cek localStorage & update <html>
onMounted(() => {
  darkMode.value = localStorage.getItem('darkMode') === 'true'
  document.documentElement.classList.toggle('dark', darkMode.value)
})

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value
  document.documentElement.classList.toggle('dark', darkMode.value)
  localStorage.setItem('darkMode', darkMode.value)
}
</script>

<template>
  <button 
    @click="toggleDarkMode"
    class="flex items-center space-x-2 px-3 py-2 rounded-md 
           bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600
           text-gray-800 dark:text-gray-100 transition"
  >
    <span v-if="darkMode">🌙 Dark</span>
    <span v-else>☀️ Light</span>
  </button>
</template>