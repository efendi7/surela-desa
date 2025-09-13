<script setup>
import { usePage } from '@inertiajs/vue3';
import { useSidebar } from '@/Composables/useSidebar';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// Ambil data user dan profil desa dari props global Inertia
const page = usePage();
const user = page.props.auth.user;
const profilDesa = page.props.profilDesa;

// Gunakan useSidebar composable
const {
    isCollapsed,
    isMobile,
    sidebarOpen,
    overlayVisible,
    toggleCollapse,
    toggleMobile,
    closeMobile
} = useSidebar();

// Handler untuk toggle sidebar (desktop dan mobile)
const handleToggleSidebar = () => {
    if (isMobile.value) {
        toggleMobile();
    } else {
        toggleCollapse();
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 relative">
        <!-- Mobile overlay -->
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
        
        <!-- Main content -->
        <div 
            :class="[
                'flex flex-col h-screen transition-all duration-300 ease-in-out relative',
                !isMobile && (isCollapsed ? 'ml-24' : 'ml-72'),
                isMobile && 'ml-0'
            ]"
            style="z-index: 1; position: relative;"
        >
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 relative z-20 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <!-- Sidebar toggle -->
                    <button
                        @click="handleToggleSidebar"
                        class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-200 z-30 relative"
                        :title="isMobile ? 'Toggle Menu' : (isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar')"
                    >
                        <!-- Mobile hamburger -->
                        <svg 
                            v-if="isMobile" 
                            class="w-6 h-6" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M4 6h16M4 12h16M4 18h16"
                            ></path>
                        </svg>
                        <!-- Desktop collapse/expand -->
                        <svg 
                            v-else
                            class="w-5 h-5" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                :d="isCollapsed ? 'M13 7l5 5-5 5M6 7l5 5-5 5' : 'M11 17l-5-5 5-5M18 17l-5-5 5-5'"
                            ></path>
                        </svg>
                    </button>

                    <!-- Header title -->
                    <div v-if="$slots.header" class="flex-1 ml-4">
                        <slot name="header" />
                    </div>
                    <div v-else class="flex-1"></div>
                    
                    <!-- User dropdown -->
                    <div class="relative z-40">
                        <Dropdown align="right" width="56">
                            <template #trigger>
                                <button class="flex items-center space-x-2 px-2 md:px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                    <!-- ✅ Foto profil atau inisial -->
                                    <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center">
                                        <img 
                                            v-if="user.profile_photo_url"
                                            :src="user.profile_photo_url" 
                                            alt="Foto Profil"
                                            class="w-full h-full object-cover"
                                        />
                                        <span v-else class="text-sm font-medium text-gray-700">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <!-- User info -->
                                    <div class="text-left hidden sm:block">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500 capitalize">{{ user.role }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <!-- User info section in dropdown -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center flex-shrink-0">
                                            <img 
                                                v-if="user.profile_photo_url"
                                                :src="user.profile_photo_url" 
                                                alt="Foto Profil"
                                                class="w-full h-full object-cover"
                                            />
                                            <span v-else class="text-lg font-medium text-gray-700">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">
                                                {{ user.email }}
                                            </div>
                                            <div class="text-xs text-gray-400 capitalize">
                                                {{ user.role }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown links -->
                                <div class="py-1">
                                    <DropdownLink :href="route('profile.edit')">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Profile
                                        </div>
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log Out
                                        </div>
                                    </DropdownLink>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="flex-1 p-4 md:p-6 relative z-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.z-25 { z-index: 25; }
.relative { position: relative; }
main { pointer-events: auto; }

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

main::-webkit-scrollbar {
    width: 6px;
}
main::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
main::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
main::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>