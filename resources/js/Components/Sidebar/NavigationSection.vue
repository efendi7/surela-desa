<template>
    <div class="space-y-3">
        <div
            v-if="!isCollapsed || isMobile"
            class="sticky top-0 z-10 bg-gradient-to-r from-slate-900/95 via-gray-900/95 to-slate-800/95 backdrop-blur-sm py-2 -mx-4 px-7 border-b border-slate-700/20"
        >
            <h3
                class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center"
            >
                <div
                    :class="[
                        'w-2 h-2 rounded-full mr-2 transition-all duration-300',
                        section.color,
                    ]"
                ></div>
                {{ section.title }}
            </h3>
        </div>
        <div class="space-y-1 pt-1">
            <template v-for="item in section.items" :key="item.name">
                <NavLink
                    :href="
                        item.routeLink
                            ? route(item.routeLink)
                            : route(...(Array.isArray(item.route) ? item.route : [item.route]))
                    "
                    :active="
                        Array.isArray(item.route)
                            ? route().current(...item.route)
                            : route().current(item.route)
                    "
                    theme="dark"
                    :class="[
                        'group relative flex items-center font-medium transition-all duration-300 ease-out overflow-visible will-change-transform transform-gpu',
                        getItemClasses(item),
                        (
                            Array.isArray(item.route)
                                ? route().current(...item.route)
                                : route().current(item.route)
                        )
                            ? '!text-black active-nav-item'
                            : 'text-slate-300 hover:text-white',
                    ]"
                    :style="getItemStyles(item)"
                    @mouseenter="handleMouseEnter(item, $event)"
                    @mouseleave="handleMouseLeave"
                    @click="emit('link-click')"
                >
                    <!-- Background Active -->
                    <div
                        v-if="
                            Array.isArray(item.route)
                                ? route().current(...item.route)
                                : route().current(item.route)
                        "
                        class="absolute inset-0 rounded-lg transition-all duration-300 ease-out"
                        :class="getBackgroundClasses(true)"
                        :style="getBackgroundStyles(true)"
                    ></div>

                    <!-- Background Hover -->
                    <div
                        v-else
                        class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out"
                        :class="getBackgroundClasses(false)"
                        :style="getBackgroundStyles(false)"
                    ></div>

                    <!-- Icon -->
                    <div
                        class="relative z-10 flex-shrink-0 transition-all duration-300 ease-out group-hover:scale-110"
                    >
                        <div
                            :class="[
                                'p-1 rounded-lg transition-all duration-300 ease-out',
                                (
                                    Array.isArray(item.route)
                                        ? route().current(...item.route)
                                        : route().current(item.route)
                                )
                                    ? 'bg-white/90 shadow-lg'
                                    : 'bg-transparent group-hover:bg-white/5',
                            ]"
                        >
                            <svg
                                :class="[
                                    'transition-all duration-300 ease-out',
                                    section.type === 'main' ? 'w-5 h-5' : 'w-4 h-4',
                                    (
                                        Array.isArray(item.route)
                                            ? route().current(...item.route)
                                            : route().current(item.route)
                                    )
                                        ? 'drop-shadow-sm !text-black'
                                        : 'text-current',
                                ]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                v-html="item.icon"
                            ></svg>
                        </div>
                    </div>

                    <!-- Text -->
                    <span
                        v-if="!isCollapsed || isMobile"
                        :class="[
                            'ml-3 relative z-10 transition-all duration-300 ease-out',
                            section.type === 'main' ? 'font-semibold' : 'text-sm font-medium',
                            (
                                Array.isArray(item.route)
                                    ? route().current(...item.route)
                                    : route().current(item.route)
                            )
                                ? 'drop-shadow-sm !text-black'
                                : '',
                        ]"
                    >
                        {{ item.name }}
                    </span>
                </NavLink>
            </template>
        </div>
    </div>
</template>

<script setup>
    import NavLink from '@/Components/NavLink.vue';
    import { Teleport, ref, computed } from 'vue';

    const props = defineProps({
        section: {
            type: Object,
            required: true,
        },
        isCollapsed: Boolean,
        isMobile: Boolean,
    });

    const emit = defineEmits(['link-click']);

    const hoveredItemInfo = ref(null);

    const getItemClasses = (item) => {
        const isActive = Array.isArray(item.route)
            ? route().current(...item.route)
            : route().current(item.route);
        const defaultRounded = props.section.type === 'main' ? 'rounded-2xl' : 'rounded-xl';

        if (props.isCollapsed && !props.isMobile) {
            return [
                'mx-2 my-0.5',
                'justify-center',
                defaultRounded,
                isActive
                    ? 'px-4 py-3 shadow-xl scale-105'
                    : 'px-4 py-3 hover:scale-105 hover:shadow-lg',
            ];
        } else {
            return [
                'mx-1 my-0.5',
                props.section.type === 'main'
                    ? isActive
                        ? `px-4 py-3 shadow-xl scale-[1.02] ${defaultRounded}`
                        : `px-4 py-3 hover:scale-[1.02] hover:shadow-lg ${defaultRounded}`
                    : isActive
                      ? `px-4 py-2.5 shadow-lg scale-[1.01] ${defaultRounded}`
                      : `px-4 py-2.5 hover:scale-[1.01] hover:shadow-md hover:bg-slate-800/30 ${defaultRounded}`,
            ];
        }
    };

    const getItemStyles = (item) => {
        const isActive = Array.isArray(item.route)
            ? route().current(...item.route)
            : route().current(item.route);

        return {
            transformOrigin: 'center',
            backfaceVisibility: 'hidden',
            perspective: '1000px',
        };
    };

    const getBackgroundClasses = (isActive) => {
        if (isActive) {
            return 'bg-gray-200 shadow-lg';
        } else {
            return 'bg-gradient-to-r from-slate-800/60 via-slate-700/60 to-slate-800/60';
        }
    };

    const getBackgroundStyles = (isActive) => {
        if (isActive) {
            return {
                boxShadow:
                    '0 8px 32px rgba(0, 0, 0, 0.2), 0 4px 16px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.3)',
                background: '#e5e7eb',
            };
        } else {
            return {
                background:
                    'linear-gradient(135deg, rgba(71, 85, 105, 0.6) 0%, rgba(51, 65, 85, 0.6) 100%)',
            };
        }
    };

    const handleMouseEnter = (item, event) => {
        if (props.isCollapsed && !isMobile) {
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
        0%,
        100% {
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
        box-shadow:
            0 20px 25px -5px rgba(0, 0, 0, 0.1),
            0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    @supports (backdrop-filter: blur(12px)) {
        .backdrop-blur-sm {
            backdrop-filter: blur(12px);
        }
    }

    .collapsed-nav-item {
        min-width: 56px;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .collapsed-nav-item svg {
        display: block;
    }

    /* Efek latar belakang aktif tembus ke kanan */
    .active-nav-item {
        position: relative;
        overflow: visible !important;
    }

    .active-nav-item::after {
        content: '';
        position: absolute;
        top: 0;
        right: -100vw; /* Memanjang ke kanan melampaui sidebar */
        height: 100%;
        width: 100vw; /* Lebar penuh layar */
        background: #e5e7eb; /* Warna latar belakang aktif */
        z-index: -1; /* Di belakang elemen lain */
        box-shadow:
            0 8px 32px rgba(0, 0, 0, 0.2),
            0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateX(100%); /* Menggeser ke kanan agar selaras dengan sidebar */
    }

    @media (max-width: 768px) {
        .active-nav-item::after {
            display: none; /* Nonaktifkan efek tembus di mode mobile */
        }
    }
</style>
