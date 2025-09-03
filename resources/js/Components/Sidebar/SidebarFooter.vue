<template>
    <div class="flex-shrink-0 p-4 border-t border-slate-700/30 bg-slate-800/30">
        <div
            :class="[
                'flex items-center',
                isCollapsed && !isMobile ? 'justify-center' : 'justify-center',
            ]"
        >
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
</template>

<script setup>
defineProps({
    isCollapsed: Boolean,
    isMobile: Boolean,
});
</script>