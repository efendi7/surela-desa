<script setup>
// Menerima props dari komponen induk
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    fileName: {
        type: String,
        default: 'Uploading...',
    },
    progress: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <teleport to="body">
        <div v-if="show" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-[9999]">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Mengunggah File</h3>
                <p class="text-sm text-gray-500 mb-4 truncate" :title="fileName">
                    {{ fileName }}
                </p>

                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                    <div 
                        class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300 ease-linear"
                        :style="{ width: progress + '%' }">
                    </div>
                </div>
                <div class="text-right text-sm font-semibold text-indigo-700">
                    {{ progress }}%
                </div>
            </div>
        </div>
    </teleport>
</template>