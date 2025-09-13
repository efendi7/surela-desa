<template>
    <aside
        :class="[
            'fixed left-0 top-0 h-screen flex flex-col transition-all duration-300 ease-out',
            'bg-gradient-to-br from-slate-900/95 via-gray-900/95 to-slate-800/95 backdrop-blur-xl',
            'border-r border-slate-700/50 shadow-2xl will-change-transform transform-gpu',
            !isMobile && (isCollapsed ? 'w-24 z-10' : 'w-72 z-20'),
            isMobile && 'w-72 transform z-30',
            isMobile && (sidebarOpen ? 'translate-x-0' : '-translate-x-full'),
        ]"
        style="backface-visibility: hidden; transform-style: preserve-3d; overflow: visible;"
    >
        <!-- Header Component -->
        <SidebarHeader
            :is-collapsed="isCollapsed"
            :is-mobile="isMobile"
            :profil-desa="profilDesa"
            @close-mobile="emit('close-mobile')"
            @link-click="handleLinkClick"
        />

        <!-- Navigation Content -->
        <nav class="flex-1 min-h-0 overflow-hidden relative">
            <!-- Background gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/10 to-slate-900/20 pointer-events-none"></div>
            
            <div 
                class="h-full overflow-y-auto smooth-scroll relative z-10" 
                :class="[isCollapsed && !isMobile ? 'px-4' : 'px-4', 'scrollbar-left']"
            >
                <div class="py-6 space-y-6">
                    <!-- Dynamic Navigation Sections -->
                    <template v-for="(section, index) in navigationSections" :key="section.id">
                        <div
                            class="transform transition-all duration-300 ease-out"
                            :style="{
                                transitionDelay: `${index * 50}ms`
                            }"
                        >
                            <NavigationSection
                                :section="section"
                                :is-collapsed="isCollapsed"
                                :is-mobile="isMobile"
                                :hovered-item="hoveredItem"
                                :tooltip-position="tooltipPosition"
                                @item-hover="handleItemHover"
                                @link-click="handleLinkClick"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Footer Component -->
        <SidebarFooter
            :is-collapsed="isCollapsed"
            :is-mobile="isMobile"
        />

        <!-- Overlay untuk mobile -->
        <div
            v-if="isMobile && sidebarOpen"
            class="fixed inset-0 bg-black/20 backdrop-blur-sm z-20 transition-opacity duration-300"
            @click="emit('close-mobile')"
        ></div>
    </aside>
</template>

<script setup>
import SidebarHeader from '@/Components/Sidebar/SidebarHeader.vue';
import NavigationSection from '@/Components/Sidebar/NavigationSection.vue';
import SidebarFooter from '@/Components/Sidebar/SidebarFooter.vue';
import { getNavigationSections } from '@/Utils/navigationConfig';
import { usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    isCollapsed: Boolean,
    isMobile: Boolean,
    sidebarOpen: Boolean,
});

const emit = defineEmits(['close-mobile']);

const profilDesa = computed(() => usePage().props.profilDesa);
const user = computed(() => usePage().props.auth.user);

const hoveredItem = ref(null);
const tooltipPosition = ref(0);

const navigationSections = computed(() => getNavigationSections(user.value));

const handleItemHover = (itemName, event) => {
    hoveredItem.value = itemName;
    if (event && event.target) {
        tooltipPosition.value = event.target.offsetTop;
    }
};

const handleLinkClick = () => {
    if (props.isMobile) {
        emit('close-mobile');
    }
};

onMounted(() => {
    const aside = document.querySelector('aside');
    if (aside) {
        aside.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    }
});
</script>

<style scoped>
/* Scrollbar di sebelah kiri */
.scrollbar-left {
    direction: rtl; /* Mengatur arah scrollbar ke kiri */
}

.scrollbar-left > * {
    direction: ltr; /* Mengembalikan arah konten ke normal */
}

.smooth-scroll {
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: rgba(99, 102, 241, 0.3) rgba(30, 41, 59, 0.8);
    overscroll-behavior: contain;
    scroll-padding-top: 60px;
    scrollbar-gutter: stable;
}

.smooth-scroll::-webkit-scrollbar {
    width: 6px;
}

.smooth-scroll::-webkit-scrollbar-track {
    background: rgba(30, 41, 59, 0.8);
    border-radius: 3px;
}

.smooth-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(99, 102, 241, 0.4) 0%, rgba(59, 130, 246, 0.4) 100%);
    border-radius: 3px;
    transition: all 0.3s ease;
}

.smooth-scroll::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, rgba(99, 102, 241, 0.6) 0%, rgba(59, 130, 246, 0.6) 100%);
}

.transform-gpu {
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-perspective: 1000px;
    perspective: 1000px;
}

.will-change-transform {
    will-change: transform, opacity;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

@supports (backdrop-filter: blur(24px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(24px) saturate(180%);
    }
}

.shadow-2xl {
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
    aside {
        -webkit-overflow-scrolling: touch;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
    }
    
    .smooth-scroll {
        -webkit-overflow-scrolling: touch;
    }
}

@media (hover: hover) {
    .group:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease-out;
    }
}

@media (prefers-color-scheme: dark) {
    .bg-gradient-to-br {
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.95) 0%, 
            rgba(17, 24, 39, 0.95) 35%, 
            rgba(15, 23, 42, 0.95) 100%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .transition-all {
        transition: none;
    }
    
    .smooth-scroll {
        scroll-behavior: auto;
    }
    
    .transform-gpu {
        transform: none;
    }
}

@media (prefers-contrast: high) {
    .border-slate-700\/50 {
        border-color: rgba(148, 163, 184, 0.8);
    }
}
</style>