<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

// Import Composables
import { useSidebar } from '@/Composables/useSidebar';
import { useDarkMode } from '@/Composables/useDarkMode';
import { useNotifications } from '@/Composables/useNotifications';

// Import Components
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// === Inisialisasi Data & State dari Composables ===
const page = usePage();
const user = computed(() => page.props.auth.user);
const profilDesa = computed(() => page.props.profilDesa);

// Destructure the specific functions needed from the composable
const { 
    isCollapsed, isMobile, sidebarOpen, 
    overlayVisible, toggleCollapse, toggleMobile, closeMobile 
} = useSidebar();

const { isDark, toggleDarkMode } = useDarkMode();
const { notifications, notificationCount, markAsRead } = useNotifications();

// Define the handler function within the component to fix the toggle issue
const handleToggleSidebar = () => {
    if (isMobile.value) {
        toggleMobile();
    } else {
        toggleCollapse();
    }
};

// === Computed property untuk margin konten utama ===
const mainContentMargin = computed(() => {
    if (isMobile.value) return '0px';
    return isCollapsed.value ? 'ml-0' : 'md:ml-72';
});

const handleNotificationClick = (notification) => {
    if (!notification.read_at) {
        markAsRead(notification.id);
    }
    // Navigasi akan ditangani oleh Inertia <Link>
};
</script>

<template>
    <div class="h-screen flex overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans">
        <!-- Overlay untuk mobile -->
        <div v-if="overlayVisible" class="fixed inset-0 bg-black bg-opacity-60 z-20 md:hidden" @click="closeMobile"></div>

        <!-- Sidebar -->
        <Sidebar 
            :user="user" 
            :profilDesa="profilDesa" 
            :is-collapsed="isCollapsed"
            :is-mobile="isMobile"
            :sidebar-open="sidebarOpen"
            @close-mobile="closeMobile"
        />

        <!-- Konten utama -->
        <div :class="['flex flex-col flex-1 w-0 transition-all duration-300 ease-in-out', mainContentMargin]">
            <!-- Header -->
            <header class="flex-shrink-0 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 md:px-6 py-3 relative z-10">
                <div class="flex justify-between items-center">
                    <!-- Tombol toggle sidebar -->
                    <button @click="handleToggleSidebar" class="p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </button>

                    <!-- Slot header opsional -->
                    <div class="flex-1 ml-4">
                        <slot name="header" />
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-4">
                        <!-- Tombol Dark Mode dengan Ikon SVG -->
                        <button @click="toggleDarkMode" class="p-2 rounded-full text-gray-500 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg v-if="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Notifikasi -->
                        <div class="relative">
                            <Dropdown align="right" width="80">
                                <template #trigger>
                                    <button class="relative p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                        </svg>
                                        <span v-if="notificationCount > 0" class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                                            {{ notificationCount }}
                                        </span>
                                    </button>
                                </template>
                                <template #content>
                                    <div class="p-2 font-bold border-b dark:border-gray-600">Notifikasi</div>
                                    <div v-if="notifications.length > 0" class="max-h-80 overflow-y-auto">
                                        <Link v-for="notif in notifications" :key="notif.id"
                                            :href="notif.data.url"
                                            @click="handleNotificationClick(notif)"
                                            class="block w-full px-4 py-3 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out"
                                            :class="{ 'font-bold bg-blue-50 dark:bg-blue-900/30': !notif.read_at }">
                                            <p class="truncate">{{ notif.data.title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ notif.data.message }}</p>
                                        </Link>
                                    </div>
                                    <div v-else class="px-4 py-3 text-sm text-center text-gray-500">
                                        Tidak ada notifikasi baru.
                                    </div>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Dropdown user -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center space-x-2">
                                        <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="w-9 h-9 rounded-full object-cover">
                                        <div v-else class="w-9 h-9 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center text-sm font-medium text-gray-700 dark:text-gray-100">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="text-left hidden sm:block">
                                            <div class="text-sm font-medium">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ user.role }}</div>
                                        </div>
                                        <!-- Chevron icon restored -->
                                        <svg class="ml-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profil Saya</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 relative overflow-y-auto p-4 md:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
/* Styling scrollbar ini bersifat global, 
  jadi lebih baik dipindahkan ke file CSS utama Anda (misal: app.css) 
  daripada menggunakan <style scoped>.
*/
main::-webkit-scrollbar {
    width: 8px;
    height: 8px;
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

/* Dark mode scrollbar */
html.dark main::-webkit-scrollbar-thumb {
    background-color: #4b5563; /* gray-600 */
}
html.dark main::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280; /* gray-500 */
}
</style>

