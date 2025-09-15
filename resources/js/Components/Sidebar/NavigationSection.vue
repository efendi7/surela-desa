<template>
    <div class="space-y-3">
        <div v-if="!isCollapsed || isMobile" class="sticky top-0 z-10 bg-gradient-to-r from-slate-900/95 via-gray-900/95 to-slate-800/95 backdrop-blur-sm py-2 -mx-4 px-7 border-b border-slate-700/20">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center">
                <div :class="['w-2 h-2 rounded-full mr-2 transition-all duration-300', section.color]"></div>
                {{ section.title }}
            </h3>
        </div>

        <div class="space-y-1 pt-1">
            <template v-for="item in section.items" :key="item.name">

                <!-- Menu dengan children -->
                <div v-if="item.children && item.children.length > 0">
                    <button
                        @click="toggleDropdown(item.name)"
                        :class="[
                            'group relative flex items-center w-full font-medium transition-all duration-300 ease-out transform-gpu',
                            getItemClasses(item),
                            isAnyChildActive(item) ? '!text-white bg-slate-800/40' : 'text-slate-300 hover:text-white'
                        ]"
                        :style="getItemStyles(item)"
                    >
                        <!-- Background untuk parent yang memiliki child aktif -->
                        <div 
                            v-if="isAnyChildActive(item)" 
                            class="absolute inset-0 rounded-lg transition-all duration-300 ease-out bg-slate-800/40 border border-slate-700/30"
                        ></div>
                        
                        <!-- Hover background -->
                        <div 
                            v-else 
                            class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out bg-slate-800/30"
                        ></div>

                        <!-- Icon container - konsisten untuk collapsed dan expanded -->
                        <div class="relative z-10 flex items-center justify-center flex-shrink-0 transition-all duration-300 ease-out group-hover:scale-110" :class="getIconContainerClasses()">
                            <div :class="['p-1 rounded-lg transition-all duration-300 ease-out', isAnyChildActive(item) ? 'bg-white/10' : 'bg-transparent group-hover:bg-white/5']">
                                <svg 
                                    :class="['transition-all duration-300 ease-out text-current', getIconSize()]" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    v-html="item.icon"
                                ></svg>
                            </div>
                        </div>

                        <!-- Label -->
                        <span v-if="!isCollapsed || isMobile" :class="['ml-3 relative z-10 transition-all duration-300 ease-out', getLabelClasses()]">
                            {{ item.name }}
                        </span>

                        <!-- Dropdown arrow -->
                        <svg 
                            v-if="!isCollapsed || isMobile" 
                            class="h-4 w-4 transition-transform duration-300 mr-2 ml-auto relative z-10" 
                            :class="{ 'rotate-90': openDropdowns[item.name] }" 
                            fill="currentColor" 
                            viewBox="0 0 20 20"
                        >
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Children dropdown -->
                    <div v-if="openDropdowns[item.name] && (!isCollapsed || isMobile)" class="pl-8 pr-2 pt-1 pb-2 space-y-1">
                        <template v-for="child in item.children" :key="child.name">
                            <NavLink
                                :href="child.routeLink ? route(child.routeLink) : route(...(Array.isArray(child.route) ? child.route : [child.route]))"
                                :active="Array.isArray(child.route) ? route().current(...child.route) : route().current(child.route)"
                                theme="dark"
                                :class="[
                                    'group relative flex items-center font-medium transition-all duration-300 ease-out text-sm px-4 py-2 rounded-xl',
                                    (Array.isArray(child.route) ? route().current(...child.route) : route().current(child.route))
                                        ? '!text-black'
                                        : 'text-slate-400 hover:text-white'
                                ]"
                                @click="emit('link-click')"
                            >
                                <div
                                    v-if="Array.isArray(child.route) ? route().current(...child.route) : route().current(child.route)"
                                    class="absolute inset-0 rounded-lg transition-all duration-300 ease-out"
                                    :class="getBackgroundClasses(true)"
                                    :style="getBackgroundStyles(true)"
                                ></div>

                                <span class="relative z-10 truncate">{{ child.name }}</span>
                            </NavLink>
                        </template>
                    </div>
                </div>

                <!-- Menu tanpa children -->
                <NavLink
                    v-else
                    :href="item.routeLink ? route(item.routeLink) : route(...(Array.isArray(item.route) ? item.route : [item.route]))"
                    :active="Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)"
                    theme="dark"
                    :class="[
                        'group relative flex items-center font-medium transition-all duration-300 ease-out transform-gpu',
                        getItemClasses(item),
                        (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)) ? '!text-white active-nav-item' : 'text-slate-300 hover:text-white',
                    ]"
                    :style="getItemStyles(item)"
                    @mouseenter="handleMouseEnter(item, $event)"
                    @mouseleave="handleMouseLeave"
                    @click="emit('link-click')"
                >
                    <!-- Active background -->
                    <div 
                        v-if="Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)" 
                        class="absolute inset-0 rounded-lg transition-all duration-300 ease-out" 
                        :class="getBackgroundClasses(true)" 
                        :style="getBackgroundStyles(true)"
                    ></div>
                    
                    <!-- Hover background -->
                    <div 
                        v-else 
                        class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out" 
                        :class="getBackgroundClasses(false)" 
                        :style="getBackgroundStyles(false)"
                    ></div>
                    
                    <!-- Icon container - konsisten untuk collapsed dan expanded -->
                    <div class="relative z-10 flex items-center justify-center flex-shrink-0 transition-all duration-300 ease-out group-hover:scale-110" :class="getIconContainerClasses()">
                        <div :class="['p-1 rounded-lg transition-all duration-300 ease-out', (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)) ? 'bg-white/90 shadow-lg' : 'bg-transparent group-hover:bg-white/5']">
                            <svg 
                                :class="['transition-all duration-300 ease-out', getIconSize(), (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)) ? 'drop-shadow-sm !text-black' : 'text-current']" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24" 
                                v-html="item.icon"
                            ></svg>
                        </div>
                    </div>
                    
                    <!-- Label -->
                    <span v-if="!isCollapsed || isMobile" :class="['ml-3 relative z-10 transition-all duration-300 ease-out', getLabelClasses(), (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)) ? 'drop-shadow-sm !text-white' : '']">
                        {{ item.name }}
                    </span>
                </NavLink>
            </template>
        </div>
    </div>
</template>

<script setup>
import NavLink from '@/Components/NavLink.vue';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    isCollapsed: Boolean,
    isMobile: Boolean,
});

const emit = defineEmits(['link-click']);

const openDropdowns = ref({});
const hoveredItemInfo = ref(null);

// Fungsi untuk toggle dropdown
const toggleDropdown = (itemName) => {
    openDropdowns.value[itemName] = !openDropdowns.value[itemName];
};

// Fungsi untuk mengecek apakah ada anak menu yang aktif
const isAnyChildActive = (parentItem) => {
    if (!parentItem.children) return false;
    return parentItem.children.some(child =>
        Array.isArray(child.route)
            ? route().current(...child.route)
            : route().current(child.route)
    );
};

// Fungsi untuk mendapatkan kelas icon container yang konsisten
const getIconContainerClasses = () => {
    if (props.isCollapsed && !props.isMobile) {
        return 'w-full h-full flex items-center justify-center'; // Full width untuk center alignment
    }
    return 'w-6 h-6 items-center justify-center';
};

// Fungsi untuk mendapatkan ukuran icon yang konsisten
const getIconSize = () => {
    return 'w-4 h-4'; // Ukuran icon konsisten untuk semua menu
};

// Fungsi untuk mendapatkan kelas label
const getLabelClasses = () => {
    return props.section.type === 'main' ? 'font-semibold' : 'text-sm font-medium';
};

// Fungsi untuk mendapatkan kelas item yang konsisten
const getItemClasses = (item) => {
    const isActive = isAnyChildActive(item) || (item.route && (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)));
    const defaultRounded = 'rounded-xl'; // Konsisten untuk semua menu

    if (props.isCollapsed && !props.isMobile) {
        return [
            'mx-2 my-1', // Margin konsisten
            'justify-center', // Center content
            'w-12 h-12', // Fixed square size untuk perfect center alignment
            defaultRounded,
            isActive
                ? 'shadow-lg'
                : 'hover:shadow-md',
        ];
    } else {
        return [
            'mx-2 my-1', // Margin konsisten
            'justify-start', // Align left untuk expanded
            'min-h-[48px]', // Height konsisten
            'px-3 py-2', // Padding konsisten
            defaultRounded,
            isActive
                ? 'shadow-lg'
                : 'hover:shadow-md',
        ];
    }
};

const getItemStyles = (item) => {
    return {
        transformOrigin: 'center',
        backfaceVisibility: 'hidden',
        perspective: '1000px',
    };
};

const getBackgroundClasses = (isActive) => {
    if (isActive) {
        return 'bg-gray-200 shadow-lg border border-gray-300';
    } else {
        return 'bg-gradient-to-r from-slate-800/50 via-slate-700/50 to-slate-800/50';
    }
};

const getBackgroundStyles = (isActive) => {
    if (isActive) {
        return {
            boxShadow: '0 8px 32px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2)',
            background: 'linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)',
        };
    } else {
        return {
            background: 'linear-gradient(135deg, rgba(71, 85, 105, 0.5) 0%, rgba(51, 65, 85, 0.5) 100%)',
        };
    }
};

const handleMouseEnter = (item, event) => {
    if (props.isCollapsed && !props.isMobile) {
        const rect = event.currentTarget.getBoundingClientRect();
        hoveredItemInfo.value = {
            name: item.name,
            top: rect.top + rect.height / 2,
            left: rect.right + 12,
        };
    }
};

const handleMouseLeave = () => {
    hoveredItemInfo.value = null;
};

// Auto-open dropdown jika ada child aktif
onMounted(() => {
    props.section.items.forEach(item => {
        if (isAnyChildActive(item)) {
            openDropdowns.value[item.name] = true;
        }
    });
});

// Close dropdown saat collapsed
watch(() => props.isCollapsed, (newVal) => {
    if (newVal && !props.isMobile) {
        openDropdowns.value = {};
    }
});
</script>

<style scoped>
.transform-gpu {
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

.group:hover .transition-all {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes pulse { 
    0%, 100% { 
        opacity: 1; 
        transform: translateY(-50%) scale(1); 
    } 
    50% { 
        opacity: 0.7; 
        transform: translateY(-50%) scale(1.1); 
    } 
}

.animate-pulse { 
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; 
}

.shadow-xl { 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
}

.shadow-2xl { 
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); 
}

@supports (backdrop-filter: blur(12px)) { 
    .backdrop-blur-sm { 
        backdrop-filter: blur(12px); 
    } 
}

/* Konsistensi untuk semua menu item */
.active-nav-item { 
    position: relative; 
    overflow: visible !important; 
}

/* Responsive design */
@media (max-width: 768px) { 
    .active-nav-item::after { 
        display: none; 
    } 
}
</style>