<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage } from '@inertiajs/vue3';

// Import komponen Sidebar
import Sidebar from '@/Components/Sidebar.vue';

// Ambil data user dan profil desa dari props global Inertia
const page = usePage();
const user = page.props.auth.user;
const profilDesa = page.props.profilDesa;
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <Sidebar :user="user" :profilDesa="profilDesa" />
        
        <!-- Main Content Area -->
        <div class="ml-64 flex flex-col">
            <!-- Top Bar dengan User Menu -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Page Title jika ada -->
                    <div v-if="$slots.header">
                        <slot name="header" />
                    </div>
                    <div v-else class="flex-1"></div>
                    
                    <!-- User Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="text-left">
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

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>