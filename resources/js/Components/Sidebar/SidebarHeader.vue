<template>
    <div class="flex-shrink-0 relative p-4 border-b border-slate-700/30">
        <!-- Mobile close button (tetap di kanan atas) -->
        <button
            v-if="isMobile"
            @click="emit('close-mobile')"
            class="absolute right-4 top-4 w-8 h-8 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors duration-200 flex items-center justify-center will-change-transform z-10"
        >
            <svg
                class="w-5 h-5 text-slate-300"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                ></path>
            </svg>
        </button>

        <!-- Logo dan Info Desa -->
        <Link
            :href="route('dashboard')"
            class="group flex items-center space-x-4 hover:bg-slate-800/50 rounded-xl p-3 transition-all duration-300 relative overflow-hidden will-change-transform"
            :title="`Ke dashboard ${profilDesa?.nama_desa || 'SURELA Desa'}`"
            @click="emit('link-click')"
        >
            <!-- Background hover effect -->
            <div
                class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
            ></div>
            
            <!-- Logo dengan status indicator -->
            <div class="relative flex-shrink-0">
                <div class="relative">
                    <img
                        :src="logoUrl"
                        :alt="`Logo ${profilDesa?.nama_desa || 'Desa'}`"
                        class="w-12 h-12 rounded-xl object-cover border border-slate-600/50 shadow-lg group-hover:border-blue-400/50 transition-all duration-300 group-hover:shadow-blue-500/20 will-change-transform"
                        @error="
                            $event.target.src =
                                'https://placehold.co/48x48/3B82F6/ffffff?text=🏛️'
                        "
                    />
                    <!-- Status indicator -->
                    <div
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full border-2 border-slate-900 animate-pulse shadow-sm"
                        title="Sistem Aktif"
                    ></div>
                </div>
            </div>
            
            <!-- Info desa - tampil saat tidak collapsed atau di mobile -->
            <div 
                v-if="!isCollapsed || isMobile" 
                class="flex-1 min-w-0 relative z-10 transition-all duration-300"
                :class="isCollapsed && !isMobile ? 'opacity-0 w-0' : 'opacity-100'"
            >
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    isCollapsed: Boolean,
    isMobile: Boolean,
    profilDesa: Object,
});

const emit = defineEmits(['close-mobile', 'link-click']);

// Computed property untuk URL logo
const logoUrl = computed(() => {
    if (props.profilDesa?.logo) {
        return `/storage/${props.profilDesa.logo}`;
    }
    return 'https://placehold.co/48x48/3B82F6/ffffff?text=🏛️';
});

// Computed property untuk menampilkan lokasi
const locationDisplay = computed(() => {
    const parts = [];
    if (props.profilDesa?.nama_kecamatan) parts.push(props.profilDesa.nama_kecamatan);
    if (props.profilDesa?.nama_kabupaten) parts.push(props.profilDesa.nama_kabupaten);
    return parts.join(' | ');
});
</script>