<script setup>
import { ref, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { useSidebar } from '@/Composables/useSidebar'
import Sidebar from '@/Components/Sidebar.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import axios from 'axios'

// === Data user & profil desa ===
const page = usePage()
const user = page.props.auth.user
const profilDesa = page.props.profilDesa

// === Sidebar logic ===
const {
  isCollapsed,
  isMobile,
  sidebarOpen,
  overlayVisible,
  toggleCollapse,
  toggleMobile,
  closeMobile
} = useSidebar()

const handleToggleSidebar = () => {
  if (isMobile.value) toggleMobile()
  else toggleCollapse()
}

// === Dark mode state ===
const darkMode = ref(false)

// === Notification state ===
const notifications = ref([])
const notificationCount = ref(0)

// === Fetch notifications ===
const fetchNotifications = async () => {
  try {
    const response = await axios.get(route('notifikasi.index'))
    notifications.value = response.data.notifications
    notificationCount.value = response.data.count
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

// === Mark notification as read ===
const markAsRead = async (notification) => {
  try {
    await axios.patch(route('notifikasi.read'), { id: notification.id })
    await fetchNotifications() // Refresh list & count
  } catch (error) {
    console.error('Failed to mark notification as read:', error)
  }
}

// === Lifecycle mount ===
onMounted(() => {
  // Dark mode from local storage
  darkMode.value = localStorage.getItem('darkMode') === 'true'
  document.documentElement.classList.toggle('dark', darkMode.value)

  // Load notifications
  fetchNotifications()
})

// === Toggle dark mode ===
const toggleDarkMode = () => {
  darkMode.value = !darkMode.value
  document.documentElement.classList.toggle('dark', darkMode.value)
  localStorage.setItem('darkMode', darkMode.value)
}
</script>

<template>
  <div class="h-screen flex overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Overlay untuk mobile -->
    <div 
      v-if="overlayVisible"
      class="fixed inset-0 bg-black bg-opacity-50 z-25 md:hidden"
      @click="closeMobile"
    ></div>

    <!-- Sidebar -->
    <Sidebar 
      :user="user" 
      :profilDesa="profilDesa" 
      :is-collapsed="isCollapsed"
      :is-mobile="isMobile"
      :sidebar-open="sidebarOpen"
      @toggle-collapse="toggleCollapse"
      @close-mobile="closeMobile"
    />

    <!-- Konten utama -->
    <div 
      :class="[
        'flex flex-col flex-1 w-0 transition-all duration-300 ease-in-out',
        !isMobile && (isCollapsed ? 'ml-0' : 'ml-72'),
        isMobile && 'ml-0'
      ]"
    >
      <!-- Header -->
      <div class="flex-shrink-0 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 md:px-6 py-4 relative z-20">
        <div class="flex justify-between items-center">
          <!-- Tombol toggle sidebar -->
          <button
            @click="handleToggleSidebar"
            class="p-2 rounded-md text-gray-600 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-200 z-30 relative"
          >
            <svg v-if="isMobile" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path 
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                :d="isCollapsed ? 'M13 7l5 5-5 5M6 7l5 5-5 5' : 'M11 17l-5-5 5-5M18 17l-5-5 5-5'" />
            </svg>
          </button>

          <!-- Slot header opsional -->
          <div v-if="$slots.header" class="flex-1 ml-4">
            <slot name="header" />
          </div>
          <div v-else class="flex-1"></div>

          <!-- Tombol dark mode -->
          <button 
            @click="toggleDarkMode"
            class="mr-4 px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 transition"
          >
            <span v-if="darkMode">🌙</span>
            <span v-else>☀️</span>
          </button>

          <!-- Notifikasi -->
          <div class="relative mr-4">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="relative inline-flex items-center p-2 rounded-md text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                  🔔
                  <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                    {{ notificationCount }}
                  </span>
                </button>
              </template>

              <template #content>
                <div v-if="notifications.length > 0" class="max-h-80 overflow-y-auto">
                 <Link v-for="notif in notifications" :key="notif.id"
      :href="notif.data.url"
      @success="markAsRead(notif)"
      class="block w-full px-4 py-2 ...">
    <p class="font-bold">{{ notif.data.title }}</p>
    <p>{{ notif.data.message }}</p>
</Link>
                </div>
                <div v-else class="px-4 py-2 text-sm text-gray-500">
                  Tidak ada notifikasi baru.
                </div>
              </template>
            </Dropdown>
          </div>

          <!-- Dropdown user -->
          <div class="relative z-40">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center space-x-2 px-2 md:px-4 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                  <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center overflow-hidden">
                    <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="w-full h-full object-cover">
                    <span v-else class="text-sm font-medium text-gray-700 dark:text-gray-100">{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div class="text-left hidden sm:block">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.role }}</div>
                  </div>
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <main class="flex-1 p-4 md:p-6 relative overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.z-25 {
  z-index: 25;
}

/* Scrollbar styling */
main::-webkit-scrollbar {
  width: 8px;
}
main::-webkit-scrollbar-track {
  background: transparent;
}
main::-webkit-scrollbar-thumb {
  background-color: #a8a8a8;
  border-radius: 20px;
  border: 3px solid transparent;
  background-clip: content-box;
}
main::-webkit-scrollbar-thumb:hover {
  background-color: #8c8c8c;
}
html.dark main::-webkit-scrollbar-thumb {
  background-color: #555;
}
html.dark main::-webkit-scrollbar-thumb:hover {
  background-color: #777;
}
</style>
