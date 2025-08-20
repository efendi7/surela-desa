<script setup>
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// Props dari parent
const props = defineProps({
    isCollapsed: Boolean,
    isMobile: Boolean,
    sidebarOpen: Boolean,
});

// Events untuk parent
const emit = defineEmits(['toggle-collapse', 'close-mobile']);

// Mengambil data dari Inertia
const profilDesa = computed(() => usePage().props.profilDesa);
const user = computed(() => usePage().props.auth.user);

// State lokal hanya untuk efek hover pada tooltip
const hoveredItem = ref(null);

// Computed property untuk URL logo
const logoUrl = computed(() => {
    if (profilDesa.value?.logo) {
        return `/storage/${profilDesa.value.logo}`;
    }
    return 'https://placehold.co/48x48/3B82F6/ffffff?text=🏛️';
});

// Computed property untuk menampilkan lokasi
const locationDisplay = computed(() => {
    const parts = [];
    if (profilDesa.value?.nama_kecamatan) parts.push(profilDesa.value.nama_kecamatan);
    if (profilDesa.value?.nama_kabupaten) parts.push(profilDesa.value.nama_kabupaten);
    return parts.join(' | ');
});

// Handler untuk toggle sidebar
const handleToggle = () => {
    emit('toggle-collapse');
};

// Handler untuk close mobile sidebar
const handleLinkClick = () => {
    if (props.isMobile) {
        emit('close-mobile');
    }
};

// Daftar item navigasi
const mainNavItems = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>`,
    },
    {
        name: 'Pengajuan Surat',
        route: 'pengajuan.*',
        routeLink: 'pengajuan.index',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>`,
    },
];

const profileNavItems = [
    {
        name: 'Sejarah Desa',
        route: ['profil.show', 'sejarah'],
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>`,
    },
    {
        name: 'Visi & Misi',
        route: ['profil.show', 'visi-misi'],
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`,
    },
    {
        name: 'Struktur Organisasi',
        route: ['profil.show', 'struktur-organisasi'],
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>`,
    },
];

const adminNavItems = [
    {
        name: 'Profil Desa',
        route: 'admin.profil-desa.*',
        routeLink: 'admin.profil-desa.index',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>`,
    },
    {
        name: 'Manajemen Surat',
        route: 'admin.jenis-surat.*',
        routeLink: 'admin.jenis-surat.index',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>`,
    },
    {
        name: 'Proses Pengajuan',
        route: 'admin.proses.*',
        routeLink: 'admin.proses.index',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>`,
    },
    {
        name: 'Kelola Pengguna',
        route: 'admin.users.*',
        routeLink: 'admin.users.index',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>`,
    },
];
</script>

<template>
    <aside
        :class="[
            'transition-all duration-300 ease-in-out backdrop-blur-xl flex flex-col',
            'bg-gradient-to-br from-slate-900/95 via-gray-900/95 to-slate-800/95',
            'border-r border-slate-700/50 shadow-2xl shadow-slate-900/20',
            // Desktop styles
            !isMobile && 'fixed left-0 top-0 h-screen z-20',
            !isMobile && (isCollapsed ? 'w-16' : 'w-72'),
            // Mobile styles
            isMobile && 'fixed left-0 top-0 h-full w-72 z-50 transform',
            isMobile && (sidebarOpen ? 'translate-x-0' : '-translate-x-full')
        ]"
    >
        <!-- Header -->
        <div class="relative p-4 border-b border-slate-700/30">
            <!-- Desktop toggle button -->
            <button
                v-if="!isMobile"
                @click="handleToggle"
                class="absolute -right-3 top-1/2 transform -translate-y-1/2 w-6 h-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full shadow-lg hover:shadow-blue-500/25 transition-all duration-200 flex items-center justify-center group z-10"
            >
                <svg
                    :class="[
                        'w-3 h-3 text-white transition-transform duration-200',
                        isCollapsed ? 'rotate-180' : '',
                    ]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    ></path>
                </svg>
            </button>

            <!-- Mobile close button -->
            <button
                v-if="isMobile"
                @click="emit('close-mobile')"
                class="absolute right-4 top-4 w-8 h-8 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <Link
                :href="route('dashboard')"
                class="group flex items-center space-x-4 hover:bg-slate-800/50 rounded-xl p-3 transition-all duration-300 relative overflow-hidden"
                :title="`Ke dashboard ${profilDesa?.nama_desa || 'SURELA Desa'}`"
                @click="handleLinkClick"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                ></div>
                <div class="relative flex-shrink-0">
                    <div class="relative">
                        <img
                            :src="logoUrl"
                            :alt="`Logo ${profilDesa?.nama_desa || 'Desa'}`"
                            class="w-12 h-12 rounded-xl object-cover border border-slate-600/50 shadow-lg group-hover:border-blue-400/50 transition-all duration-300 group-hover:shadow-blue-500/20"
                            @error="
                                $event.target.src =
                                    'https://placehold.co/48x48/3B82F6/ffffff?text=🏛️'
                            "
                        />
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full border-2 border-slate-900 animate-pulse shadow-sm"
                            title="Sistem Aktif"
                        ></div>
                    </div>
                </div>
                <div v-if="!isCollapsed || isMobile" class="flex-1 min-w-0 relative z-10">
                    <div
                        class="font-bold text-slate-100 text-lg truncate group-hover:text-white transition-colors duration-300"
                    >
                        {{ profilDesa?.nama_desa || 'SURELA Desa' }}
                    </div>
                    <div
                        v-if="locationDisplay"
                        class="text-xs text-slate-400 truncate mt-1 group-hover:text-slate-300 transition-colors duration-300"
                    >
                        {{ locationDisplay }}
                    </div>
                </div>
            </Link>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar">
            <div :class="['py-6 space-y-6', (isCollapsed && !isMobile) ? 'px-2' : 'px-4']">
                <!-- Main Navigation -->
                <div class="space-y-2">
                    <h3
                        v-if="!isCollapsed || isMobile"
                        class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center"
                    >
                        <div
                            class="w-2 h-2 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full mr-2"
                        ></div>
                        Menu Utama
                    </h3>
                    <div class="space-y-1">
                        <template v-for="item in mainNavItems" :key="item.name">
                            <NavLink
                                :href="route(item.routeLink || item.route)"
                                :active="route().current(item.route)"
                                theme="dark"
                                :class="[
                                    'group relative flex items-center rounded-xl font-medium transition-all duration-300 overflow-hidden',
                                    (isCollapsed && !isMobile) ? 'px-3 py-3 justify-center' : 'px-4 py-3',
                                    route().current(item.route)
                                        ? 'text-white shadow-lg'
                                        : 'text-slate-300 hover:text-white',
                                ]"
                                @mouseenter="hoveredItem = item.name"
                                @mouseleave="hoveredItem = null"
                                @click="handleLinkClick"
                            >
                                <div
                                    v-if="route().current(item.route)"
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 opacity-90"
                                ></div>
                                <div
                                    v-else
                                    class="absolute inset-0 bg-gradient-to-r from-slate-800/50 to-slate-700/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                                <div class="relative z-10 flex-shrink-0">
                                    <svg
                                        class="w-5 h-5 transition-transform duration-200 group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        v-html="item.icon"
                                    ></svg>
                                </div>
                                <span
                                    v-if="!isCollapsed || isMobile"
                                    class="ml-3 relative z-10 transition-all duration-200"
                                    >{{ item.name }}</span
                                >
                                <!-- Desktop tooltip -->
                                <div
                                    v-if="isCollapsed && !isMobile && hoveredItem === item.name"
                                    class="absolute left-full ml-2 px-3 py-2 bg-slate-800 text-white text-sm rounded-lg shadow-lg border border-slate-600/50 whitespace-nowrap z-50"
                                >
                                    {{ item.name }}
                                    <div
                                        class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-slate-800 rotate-45 border-l border-b border-slate-600/50"
                                    ></div>
                                </div>
                            </NavLink>
                        </template>
                    </div>
                </div>

                <!-- Profile Navigation -->
                <div class="space-y-2">
                    <h3
                        v-if="!isCollapsed || isMobile"
                        class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center"
                    >
                        <div
                            class="w-2 h-2 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full mr-2"
                        ></div>
                        Profil Desa
                    </h3>
                    <div class="space-y-1">
                        <template v-for="item in profileNavItems" :key="item.name">
                            <NavLink
                                :href="route(...item.route)"
                                :active="route().current(...item.route)"
                                theme="dark"
                                :class="[
                                    'group relative flex items-center rounded-xl font-medium transition-all duration-300 text-slate-300 hover:text-white hover:bg-slate-800/50',
                                    (isCollapsed && !isMobile) ? 'px-3 py-3 justify-center' : 'px-4 py-2.5',
                                ]"
                                @mouseenter="hoveredItem = item.name"
                                @mouseleave="hoveredItem = null"
                                @click="handleLinkClick"
                            >
                                <div class="flex-shrink-0">
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200 group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        v-html="item.icon"
                                    ></svg>
                                </div>
                                <span v-if="!isCollapsed || isMobile" class="ml-3 text-sm">{{
                                    item.name
                                }}</span>
                                <!-- Desktop tooltip -->
                                <div
                                    v-if="isCollapsed && !isMobile && hoveredItem === item.name"
                                    class="absolute left-full ml-2 px-3 py-2 bg-slate-800 text-white text-sm rounded-lg shadow-lg border border-slate-600/50 whitespace-nowrap z-50"
                                >
                                    {{ item.name }}
                                    <div
                                        class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-slate-800 rotate-45 border-l border-b border-slate-600/50"
                                    ></div>
                                </div>
                            </NavLink>
                        </template>
                    </div>
                </div>

                <!-- Admin Navigation -->
                <div v-if="user?.role === 'admin'" class="space-y-2">
                    <h3
                        v-if="!isCollapsed || isMobile"
                        class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center"
                    >
                        <div
                            class="w-2 h-2 bg-gradient-to-r from-orange-400 to-red-500 rounded-full mr-2 animate-pulse"
                        ></div>
                        Menu Admin
                    </h3>
                    <div class="space-y-1">
                        <template v-for="item in adminNavItems" :key="item.name">
                            <NavLink
                                :href="route(item.routeLink || item.route)"
                                :active="route().current(item.route)"
                                theme="dark"
                                :class="[
                                    'group relative flex items-center rounded-xl font-medium transition-all duration-300 overflow-hidden',
                                    (isCollapsed && !isMobile) ? 'px-3 py-3 justify-center' : 'px-4 py-3',
                                    route().current(item.route)
                                        ? 'text-white shadow-lg'
                                        : 'text-slate-300 hover:text-white',
                                ]"
                                @mouseenter="hoveredItem = item.name"
                                @mouseleave="hoveredItem = null"
                                @click="handleLinkClick"
                            >
                                <div
                                    v-if="route().current(item.route)"
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 opacity-90"
                                ></div>
                                <div
                                    v-else
                                    class="absolute inset-0 bg-gradient-to-r from-slate-800/50 to-slate-700/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                                <div class="relative z-10 flex-shrink-0">
                                    <svg
                                        class="w-5 h-5 transition-transform duration-200 group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        v-html="item.icon"
                                    ></svg>
                                </div>
                                <span
                                    v-if="!isCollapsed || isMobile"
                                    class="ml-3 relative z-10 transition-all duration-200"
                                    >{{ item.name }}</span
                                >
                                <!-- Desktop tooltip -->
                                <div
                                    v-if="isCollapsed && !isMobile && hoveredItem === item.name"
                                    class="absolute left-full ml-2 px-3 py-2 bg-slate-800 text-white text-sm rounded-lg shadow-lg border border-slate-600/50 whitespace-nowrap z-50"
                                >
                                    {{ item.name }}
                                    <div
                                        class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-slate-800 rotate-45 border-l border-b border-slate-600/50"
                                    ></div>
                                </div>
                            </NavLink>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Footer Status -->
        <div class="p-4 border-t border-slate-700/30 bg-slate-800/30">
            <div :class="['flex items-center', (isCollapsed && !isMobile) ? 'justify-center' : 'justify-center']">
                <div class="flex items-center space-x-2 text-xs text-slate-400">
                    <div class="relative">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <div
                            class="absolute inset-0 w-2 h-2 bg-emerald-400 rounded-full animate-ping opacity-75"
                        ></div>
                    </div>
                    <span v-if="!isCollapsed || isMobile" class="font-medium">Sistem Aktif</span>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #475569 #1e293b;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #1e293b;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 2px;
    transition: background 0.2s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>