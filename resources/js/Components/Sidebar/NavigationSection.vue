<template>
    <div class="space-y-3">
        <div 
            v-if="!isCollapsed || isMobile"
            class="sticky top-0 z-10 bg-gradient-to-r from-slate-900/95 via-gray-900/95 to-slate-800/95 backdrop-blur-sm py-2 -mx-4 px-7 border-b border-slate-700/20"
        >
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center">
                <div 
                    :class="[
                        'w-2 h-2 rounded-full mr-2',
                        section.color
                    ]"
                ></div>
                {{ section.title }}
            </h3>
        </div>
        <div class="space-y-1 pt-1">
            <template v-for="item in section.items" :key="item.name">
                <NavLink
                    :href="item.routeLink ? route(item.routeLink) : route(...(Array.isArray(item.route) ? item.route : [item.route]))"
                    :active="Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)"
                    theme="dark"
                    :class="[
                        'group relative flex items-center rounded-xl font-medium transition-all duration-200 overflow-hidden will-change-transform',
                        isCollapsed && !isMobile
                            ? 'px-3 py-3 justify-center'
                            : section.type === 'main' ? 'px-4 py-3' : 'px-4 py-2.5',
                        (Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route))
                            ? 'text-white shadow-lg'
                            : 'text-slate-300 hover:text-white',
                        section.type !== 'main' && 'hover:bg-slate-800/50'
                    ]"
                    @mouseenter="emit('item-hover', item.name)"
                    @mouseleave="emit('item-hover', null)"
                    @click="emit('link-click')"
                >
                    <div
                        v-if="(Array.isArray(item.route) ? route().current(...item.route) : route().current(item.route)) && section.type === 'main'"
                        class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 opacity-90"
                    ></div>
                    <div
                        v-else-if="section.type === 'main'"
                        class="absolute inset-0 bg-gradient-to-r from-slate-800/50 to-slate-700/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    ></div>
                    <div class="relative z-10 flex-shrink-0">
                        <svg
                            :class="[
                                'transition-transform duration-200 group-hover:scale-110',
                                section.type === 'main' ? 'w-5 h-5' : 'w-4 h-4'
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="item.icon"
                        ></svg>
                    </div>
                    <span
                        v-if="!isCollapsed || isMobile"
                        :class="[
                            'ml-3 relative z-10 transition-all duration-200',
                            section.type === 'main' ? '' : 'text-sm'
                        ]"
                    >
                        {{ item.name }}
                    </span>
                    <!-- Desktop tooltip -->
                    <Teleport to="body">
                        <div
                            v-if="isCollapsed && !isMobile && hoveredItem === item.name"
                            class="fixed px-3 py-2 bg-slate-800 text-white text-sm rounded-lg shadow-lg border border-slate-600/50 whitespace-nowrap z-50 pointer-events-none"
                            :style="{
                                left: '80px',
                                top: tooltipPosition + 'px'
                            }"
                        >
                            {{ item.name }}
                        </div>
                    </Teleport>
                </NavLink>
            </template>
        </div>
    </div>
</template>

<script setup>
import NavLink from '@/Components/NavLink.vue';
import { Teleport } from 'vue';

defineProps({
    section: {
        type: Object,
        required: true
    },
    isCollapsed: Boolean,
    isMobile: Boolean,
    hoveredItem: String,
    tooltipPosition: Number,
});

const emit = defineEmits(['item-hover', 'link-click']);
</script>