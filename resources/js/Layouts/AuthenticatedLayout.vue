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
        <!-- Mobile overlay dengan z-index yang tepat -->
        <div 
            v-if="overlayVisible"
            class="fixed inset-0 bg-black bg-opacity-50 z-25 md:hidden"
            @click="closeMobile"
        ></div>

        <!-- Sidebar dengan z-index yang dinamis -->
        <Sidebar 
            :user="user" 
            :profilDesa="profilDesa" 
            :is-collapsed="isCollapsed"
            :is-mobile="isMobile"
            :sidebar-open="sidebarOpen"
            @toggle-collapse="toggleCollapse"
            @close-mobile="closeMobile"
        />
        
        <!-- Main content area dengan z-index yang tepat -->
        <div 
            :class="[
                'flex flex-col h-screen transition-all duration-300 ease-in-out relative',
                // Desktop behavior
                !isMobile && (isCollapsed ? 'ml-16' : 'ml-72'),
                // Mobile behavior - no margin, full width
                isMobile && 'ml-0'
            ]"
            style="z-index: 1; position: relative;"
        >
            <!-- Fixed Header dengan z-index yang tepat -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 relative z-20 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <!-- Toggle button (mobile & desktop) -->
                    <button
                        @click="handleToggleSidebar"
                        class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-200 z-30 relative"
                        :title="isMobile ? 'Toggle Menu' : (isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar')"
                    >
                        <!-- Mobile: hamburger icon -->
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
                        <!-- Desktop: collapse/expand icon -->
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

                    <!-- Header content -->
                    <div v-if="$slots.header" class="flex-1 ml-4">
                        <slot name="header" />
                    </div>
                    <div v-else class="flex-1"></div>
                    
                    <!-- User dropdown dengan z-index tinggi -->
                    <div class="relative z-40">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 px-2 md:px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                        <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="w-full h-full object-cover">
                                        <span v-else class="text-sm font-medium text-gray-700">{{ user.name.charAt(0).toUpperCase() }}</span>
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

            <!-- Scrollable Main content dengan overflow -->
            <main class="flex-1 p-4 md:p-6 relative z-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom z-index utilities */
.z-25 {
    z-index: 25;
}

/* Ensure proper stacking context */
.relative {
    position: relative;
}

/* Prevent pointer events interference */
main {
    pointer-events: auto;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar untuk webkit browsers */
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