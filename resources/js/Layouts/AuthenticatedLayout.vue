<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// Ambil data user dan profil desa dari props global Inertia
const page = usePage();
const user = page.props.auth.user;
const profilDesa = page.props.profilDesa;

// State untuk mengontrol kondisi sidebar dan mobile
const isCollapsed = ref(false);
const isMobile = ref(false);
const sidebarOpen = ref(false);

// Fungsi untuk mengecek apakah device mobile
const checkMobile = () => {
    isMobile.value = window.innerWidth < 768; // md breakpoint
    if (isMobile.value) {
        isCollapsed.value = false; // Reset collapsed state on mobile
    }
};

// Fungsi untuk mengubah state sidebar
const toggleSidebar = () => {
    if (isMobile.value) {
        sidebarOpen.value = !sidebarOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};

// Fungsi untuk menutup sidebar mobile ketika klik di luar
const closeMobileSidebar = () => {
    if (isMobile.value) {
        sidebarOpen.value = false;
    }
};

// Setup event listeners
onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile overlay -->
        <div 
            v-if="isMobile && sidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
            @click="closeMobileSidebar"
        ></div>

        <Sidebar 
            :user="user" 
            :profilDesa="profilDesa" 
            :is-collapsed="isCollapsed"
            :is-mobile="isMobile"
            :sidebar-open="sidebarOpen"
            @toggle-collapse="toggleSidebar"
            @close-mobile="closeMobileSidebar"
        />
        
        <div 
            :class="[
                'flex flex-col transition-all duration-300 ease-in-out',
                // Desktop behavior
                !isMobile && (isCollapsed ? 'ml-16' : 'ml-72'),
                // Mobile behavior - no margin, full width
                isMobile && 'ml-0'
            ]"
        >
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Mobile menu button -->
                    <button
                        v-if="isMobile"
                        @click="toggleSidebar"
                        class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Header content -->
                    <div v-if="$slots.header" class="flex-1" :class="isMobile ? 'ml-4' : ''">
                        <slot name="header" />
                    </div>
                    <div v-else class="flex-1"></div>
                    
                    <!-- User dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 px-2 md:px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <!-- Hide text on very small screens -->
                                    <div class="text-left hidden sm:block">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ user.role }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="flex-1 p-4 md:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>