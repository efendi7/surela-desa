<!-- File: @/Components/ProcessDropdown.vue -->
<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Props
defineProps({
    item: {
        type: Object,
        required: true
    }
})

// Emits
defineEmits([
    'open-detail',
    'trigger-upload', 
    'confirm-selesai',
    'delete-file'
])

// State untuk dropdown
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

// Toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

// Close dropdown
const closeDropdown = () => {
    isDropdownOpen.value = false
}

// Handle click outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown()
    }
}

// Mount/unmount event listeners
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div class="relative inline-block text-left" ref="dropdownRef">
        <!-- Main Process Button -->
        <button @click="toggleDropdown"
                class="inline-flex items-center justify-center px-3 py-2 border border-indigo-300 text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            Proses Surat
            <svg class="w-4 h-4 ml-1 transform transition-transform duration-150" 
                 :class="{ 'rotate-180': isDropdownOpen }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div v-if="isDropdownOpen" 
             class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 transform opacity-100 scale-100 transition-all duration-150 ease-out">
            
            <!-- Ubah Status -->
            <button @click="$emit('open-detail', item); closeDropdown()"
                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center transition-colors duration-150">
                <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Ubah Status
            </button>

            <!-- Divider -->
            <div v-if="item.status === 'diproses'" class="border-t border-gray-100 my-2"></div>

            <!-- Actions for "diproses" status -->
            <template v-if="item.status === 'diproses'">
                <!-- Generate Surat -->
                <a :href="route('admin.pengajuan.generate', { pengajuan: item.id })"
                   @click="closeDropdown"
                   class="w-full px-4 py-2 text-left text-sm text-purple-700 hover:bg-purple-50 flex items-center transition-colors duration-150">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Surat
                </a>

                <!-- File Status & Upload -->
                <div v-if="!item.file_final">
                    <button @click="$emit('trigger-upload', item); closeDropdown()"
                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Hasil
                    </button>
                </div>

                <!-- File Ready Status -->
                <div v-else>
                    <!-- File Ready Indicator -->
                    <div class="px-4 py-2 text-sm text-green-700 bg-green-50 mx-2 my-1 rounded flex items-center">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        File Siap
                    </div>

                    <!-- File Management -->
                    <div class="flex mx-2 my-1 space-x-1">
                        <button @click="$emit('trigger-upload', item); closeDropdown()"
                                class="flex-1 px-2 py-1.5 text-xs text-blue-700 bg-blue-50 hover:bg-blue-100 rounded flex items-center justify-center transition-colors duration-150"
                                title="Ubah File">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" />
                            </svg>
                        </button>
                        <button @click="$emit('delete-file', item); closeDropdown()"
                                class="flex-1 px-2 py-1.5 text-xs text-red-700 bg-red-50 hover:bg-red-100 rounded flex items-center justify-center transition-colors duration-150"
                                title="Hapus File">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Divider before final action -->
                <div class="border-t border-gray-100 my-2"></div>

                <!-- Confirm Selesai -->
                <button @click="$emit('confirm-selesai', item); closeDropdown()"
                        :disabled="!item.file_final"
                        :class="[
                            'w-full px-4 py-2.5 text-left text-sm font-semibold flex items-center transition-colors duration-150',
                            item.file_final 
                                ? 'text-white bg-green-600 hover:bg-green-700 mx-2 mb-1 rounded' 
                                : 'text-gray-400 cursor-not-allowed'
                        ]">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Konfirmasi Selesai
                </button>
            </template>

            <!-- Pending Status Message -->
            <div v-else-if="item.status === 'pending'" class="px-4 py-3">
                <div class="inline-flex items-center text-xs text-yellow-800 bg-yellow-50 border border-yellow-200 rounded px-2 py-1">
                    <svg class="w-3 h-3 mr-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <span>Ubah status ke 'diproses'</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transitions */
.transform {
    transition: transform 150ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Ensure high z-index for dropdown */
.z-50 {
    z-index: 50;
}

/* Prevent overflow issues */
.dropdown-menu {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 0.5rem;
    width: 12rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid #e5e7eb;
    padding: 0.5rem 0;
    z-index: 50;
}
</style>