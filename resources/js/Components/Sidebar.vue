<script setup>
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Mengambil profilDesa dari shared data Inertia
const profilDesa = computed(() => usePage().props.profilDesa);
const user = computed(() => usePage().props.auth.user);


// Computed property for logo URL
const logoUrl = computed(() => {
    if (profilDesa.value?.logo) {
        return `/storage/${profilDesa.value.logo}`;
    }
    return "https://placehold.co/48x48/3B82F6/ffffff?text=🏛️";
});

// Computed property for village location display
const locationDisplay = computed(() => {
    const parts = [];
    // Menggunakan nama properti yang benar: nama_kecamatan dan nama_kabupaten
    if (profilDesa.value?.nama_kecamatan) parts.push(profilDesa.value.nama_kecamatan);
    if (profilDesa.value?.nama_kabupaten) parts.push(profilDesa.value.nama_kabupaten);
    // Menggunakan ' | ' sebagai pemisah
    return parts.join(' | ');
});
</script>

<template>
    <aside class="fixed left-0 top-0 w-64 bg-gradient-to-b from-gray-800 to-gray-900 text-white flex flex-col h-screen shadow-xl z-20">
        <!-- Header Sidebar dengan Info Desa Dinamis -->
        <div class="p-6 border-b border-gray-700/50 bg-gray-800/50">
            <Link 
                :href="route('dashboard')" 
                class="group flex items-center space-x-4 hover:bg-gray-700/30 rounded-lg p-3 transition-all duration-200"
                :title="`Ke dashboard ${profilDesa?.nama_desa || 'SURELA Desa'}`"
            >
                <div class="relative flex-shrink-0">
                    <img 
                        :src="logoUrl"
                        :alt="`Logo ${profilDesa?.nama_desa || 'Desa'}`"
                        class="w-12 h-12 rounded-full object-cover border-2 border-blue-400/50 shadow-lg group-hover:border-blue-400 transition-colors duration-200"
                        @error="$event.target.src = 'https://placehold.co/48x48/3B82F6/ffffff?text=🏛️'"
                    >
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-800" title="Sistem Aktif"></div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-white text-base truncate group-hover:text-blue-200 transition-colors duration-200">
                        {{ profilDesa?.nama_desa || 'SURELA Desa' }}
                    </div>
                    <div v-if="locationDisplay" class="text-xs text-gray-300 truncate mt-1 opacity-80">
                        {{ locationDisplay }}
                    </div>
                </div>
            </Link>
        </div>
        
        <!-- Navigation Menu dengan Scroll -->
        <nav class="flex-1 overflow-y-auto scrollbar-thin scrollbar-track-gray-800 scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500">
            <div class="px-4 py-6 space-y-2">
                <!-- Menu Utama Warga -->
                <div class="space-y-1">
                    <NavLink 
                        :href="route('dashboard')" 
                        :active="route().current('dashboard')" 
                        theme="dark"
                        class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                        :class="route().current('dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300'"
                    >
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </NavLink>
                    
                    <NavLink 
                        :href="route('pengajuan.index')" 
                        :active="route().current('pengajuan.*')" 
                        theme="dark"
                        class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                        :class="route().current('pengajuan.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300'"
                    >
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Pengajuan Surat
                    </NavLink>
                </div>

                <!-- Menu Khusus Admin -->
                <div v-if="user?.role === 'admin'" class="pt-6 mt-6 border-t border-gray-700/50">
                    <div class="flex items-center px-3 mb-4">
                        <div class="w-2 h-2 bg-orange-400 rounded-full mr-2"></div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Menu Admin
                        </h3>
                    </div>
                    
                    <div class="space-y-1">
                        <NavLink 
                            :href="route('admin.profil-desa.index')" 
                            :active="route().current('admin.profil-desa.*')" 
                            theme="dark"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                            :class="route().current('admin.profil-desa.*') ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-300'"
                        >
                            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Profil Desa
                        </NavLink>
                        
                        <NavLink 
                            :href="route('admin.jenis-surat.index')" 
                            :active="route().current('admin.jenis-surat.*')" 
                            theme="dark"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                            :class="route().current('admin.jenis-surat.*') ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-300'"
                        >
                            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                            Manajemen Surat
                        </NavLink>
                        
                        <NavLink 
                            :href="route('admin.pengajuan.index')" 
                            :active="route().current('admin.pengajuan.*')" 
                            theme="dark"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                            :class="route().current('admin.pengajuan.*') ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-300'"
                        >
                            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Proses Pengajuan
                        </NavLink>

                        <!-- User Management (Optional) -->
                        <NavLink 
                            :href="route('admin.users.index')" 
                            :active="route().current('admin.users.*')" 
                            theme="dark"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200"
                            :class="route().current('admin.users.*') ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-300'"
                            v-if="route().has('admin.users.index')"
                        >
                            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Kelola Pengguna
                        </NavLink>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-gray-700/50 bg-gray-800/30">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-2 text-xs text-gray-400">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span>Sistem Aktif</span>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* Custom Scrollbar */
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}

.scrollbar-track-gray-800::-webkit-scrollbar-track {
    background-color: #1f2937;
    border-radius: 2px;
}

.scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
    background-color: #4b5563;
    border-radius: 2px;
}

.scrollbar-thumb-gray-600:hover::-webkit-scrollbar-thumb {
    background-color: #6b7280;
}

/* Smooth scrolling */
nav {
    scroll-behavior: smooth;
}

/* Loading state for images */
img {
    transition: opacity 0.2s ease-in-out;
}

img[src*="placehold.co"] {
    opacity: 0.8;
}
</style>
