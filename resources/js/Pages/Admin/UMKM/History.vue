<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    umkms: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const selectedFilter = ref(props.filters.status || '');

const stats = computed(() => {
    const data = props.umkms.data || [];
    const total = props.umkms.total || 0;
    const disetujui = data.filter(umkm => umkm.status === 'disetujui').length;
    const ditolak = data.filter(umkm => umkm.status === 'ditolak').length;
    
    return { total, disetujui, ditolak };
});

const applyFilter = (status) => {
    selectedFilter.value = status;
    router.get(route('admin.umkm.history'), { 
        status: status || undefined 
    }, { 
        preserveState: true, 
        preserveScroll: true 
    });
};

const getFotoUrl = (path) => {
    return path ? `/storage/${path.replace('public/', '')}` : 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image';
};

const formatDate = (dateString) => {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        timeZone: 'Asia/Jakarta'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const statusClass = (status) => {
    switch (status) {
        case 'disetujui':
            return 'bg-green-100 text-green-800 border-green-200';
        case 'ditolak':
            return 'bg-red-100 text-red-800 border-red-200';
        default:
            return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const formatStatus = (status) => {
    const statusMap = {
        'disetujui': 'Disetujui',
        'ditolak': 'Ditolak'
    };
    return statusMap[status] || status;
};
</script>

<template>
    <Head title="Riwayat UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat UMKM</h2>
                <p class="text-sm text-gray-600 mt-1">Lihat riwayat UMKM yang telah diverifikasi</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filter Buttons -->
                <div class="mb-6 flex flex-wrap gap-2">
                    <button 
                        @click="applyFilter('')"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg border transition-colors',
                            selectedFilter === '' 
                                ? 'bg-indigo-600 text-white border-indigo-600' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        Semua ({{ stats.total }})
                    </button>
                    <button 
                        @click="applyFilter('disetujui')"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg border transition-colors',
                            selectedFilter === 'disetujui' 
                                ? 'bg-green-600 text-white border-green-600' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        Disetujui ({{ stats.disetujui }})
                    </button>
                    <button 
                        @click="applyFilter('ditolak')"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg border transition-colors',
                            selectedFilter === 'ditolak' 
                                ? 'bg-red-600 text-white border-red-600' 
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        Ditolak ({{ stats.ditolak }})
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Total UMKM</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ stats.total }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Disetujui</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ stats.disetujui }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ stats.ditolak }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
                    <div class="p-6">
                        <div v-if="umkms.data.length > 0">
                            <!-- Desktop Table View -->
                            <div class="hidden lg:block overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Usaha</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Aksi</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="umkm in umkms.data" :key="umkm.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-16 w-16">
                                                        <img 
                                                            class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200" 
                                                            :src="getFotoUrl(umkm.foto_produk)" 
                                                            :alt="`Foto produk ${umkm.nama_usaha}`"
                                                            @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                                                        >
                                                    </div>
                                                    <div class="ml-4 min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 truncate">{{ umkm.nama_usaha }}</div>
                                                        <div class="text-sm text-gray-500">{{ umkm.kategori_usaha }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ umkm.user?.name || 'N/A' }}</div>
                                                <div class="text-xs text-gray-400 mt-1">{{ umkm.nomor_telepon }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full border" :class="statusClass(umkm.status)">
                                                    {{ formatStatus(umkm.status) }}
                                                </span>
                                                <div v-if="umkm.status === 'ditolak' && umkm.catatan_admin" class="text-xs text-red-600 mt-2 p-2 bg-red-50 rounded border-l-2 border-red-200">
                                                    <strong>Catatan:</strong><br>
                                                    {{ umkm.catatan_admin }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(umkm.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <Link 
                                                        :href="route('admin.umkm.show', umkm.id)" 
                                                        class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Lihat
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="lg:hidden space-y-4">
                                <div v-for="umkm in umkms.data" :key="umkm.id" class="border border-gray-200 rounded-lg p-4 space-y-3">
                                    <div class="flex items-start space-x-3">
                                        <img 
                                            class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200" 
                                            :src="getFotoUrl(umkm.foto_produk)" 
                                            :alt="`Foto produk ${umkm.nama_usaha}`"
                                            @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ umkm.nama_usaha }}</h3>
                                            <p class="text-sm text-gray-500">{{ umkm.kategori_usaha }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ umkm.user?.name || 'N/A' }}</p>
                                        </div>
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full border shrink-0" :class="statusClass(umkm.status)">
                                            {{ formatStatus(umkm.status) }}
                                        </span>
                                    </div>
                                    
                                    <div v-if="umkm.status === 'ditolak' && umkm.catatan_admin" class="text-xs text-red-600 p-2 bg-red-50 rounded border-l-2 border-red-200">
                                        <strong>Catatan:</strong><br>
                                        {{ umkm.catatan_admin }}
                                    </div>
                                    
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">{{ formatDate(umkm.created_at) }}</span>
                                        <div class="flex space-x-2">
                                            <Link 
                                                :href="route('admin.umkm.show', umkm.id)" 
                                                class="inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50"
                                            >
                                                Lihat
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                <Pagination :links="umkms.links" />
                            </div>
                        </div>

                        <!-- No Data -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-700">
                                {{ selectedFilter ? `Tidak Ada UMKM dengan Status ${formatStatus(selectedFilter)}` : 'Belum Ada Riwayat UMKM' }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                                {{ selectedFilter ? 'Coba ubah filter untuk melihat data lainnya.' : 'Belum ada UMKM yang telah diverifikasi.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.transition-colors {
    transition-property: background-color, border-color, color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.rounded-full {
    border-radius: 9999px;
}

img {
    transition: all 0.3s ease;
}

img:hover {
    transform: scale(1.05);
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .print-friendly {
        background: white !important;
        color: black !important;
    }
}

@media (max-width: 768px) {
    .mobile-stack {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-full-width {
        width: 100%;
    }
}
</style>