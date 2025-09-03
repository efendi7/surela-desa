<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        default: 1
    },
    totalPages: {
        type: Number,
        required: true
    },
    perPage: {
        type: Number,
        default: 10
    },
    totalItems: {
        type: Number,
        required: true
    },
    showInfo: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['page-changed']);

const pageNumbers = computed(() => {
    const pages = [];
    const total = props.totalPages;
    const current = props.currentPage;
    
    // Always show first page
    if (total > 0) pages.push(1);
    
    let start = Math.max(2, current - 2);
    let end = Math.min(total - 1, current + 2);
    
    // Add ellipsis after first page if needed
    if (start > 2) {
        pages.push('...');
    }
    
    // Add pages around current
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    // Add ellipsis before last page if needed
    if (end < total - 1) {
        pages.push('...');
    }
    
    // Always show last page (if different from first)
    if (total > 1) pages.push(total);
    
    return pages;
});

const startItem = computed(() => {
    return ((props.currentPage - 1) * props.perPage) + 1;
});

const endItem = computed(() => {
    return Math.min(props.currentPage * props.perPage, props.totalItems);
});

const canGoPrev = computed(() => props.currentPage > 1);
const canGoNext = computed(() => props.currentPage < props.totalPages);

const changePage = (page) => {
    if (page !== '...' && page >= 1 && page <= props.totalPages && page !== props.currentPage) {
        emit('page-changed', page);
    }
};
</script>

<template>
    <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
        <!-- Mobile pagination -->
        <div class="flex justify-between flex-1 sm:hidden">
            <button
                @click="changePage(currentPage - 1)"
                :disabled="!canGoPrev"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Previous
            </button>
            <button
                @click="changePage(currentPage + 1)"
                :disabled="!canGoNext"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Next
            </button>
        </div>
        
        <!-- Desktop pagination -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div v-if="showInfo">
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ startItem }}</span>
                    to
                    <span class="font-medium">{{ endItem }}</span>
                    of
                    <span class="font-medium">{{ totalItems }}</span>
                    results
                </p>
            </div>
            
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Previous button -->
                    <button
                        @click="changePage(currentPage - 1)"
                        :disabled="!canGoPrev"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="sr-only">Previous</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <!-- Page numbers -->
                    <template v-for="page in pageNumbers" :key="page">
                        <span
                            v-if="page === '...'"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300"
                        >
                            ...
                        </span>
                        <button
                            v-else
                            @click="changePage(page)"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-medium border',
                                page === currentPage
                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </template>
                    
                    <!-- Next button -->
                    <button
                        @click="changePage(currentPage + 1)"
                        :disabled="!canGoNext"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="sr-only">Next</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>