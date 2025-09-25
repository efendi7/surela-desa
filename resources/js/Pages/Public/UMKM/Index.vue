<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    umkms: {
        type: Object,
        required: true,
    },
    search: {
        type: String,
        default: '',
    },
    kategori: {
        type: String,
        default: '',
    },
});

const searchQuery = ref(props.search || '');
const selectedKategori = ref(props.kategori || '');

const kategoriUsaha = [
    'Semua Kategori',
    'Makanan & Minuman',
    'Fashion & Aksesori',
    'Kerajinan Tangan',
    'Teknologi & Digital',
    'Jasa & Layanan',
    'Pertanian & Perkebunan',
    'Peternakan & Perikanan',
    'Lainnya',
];

const stats = computed(() => {
    const total = props.umkms.total || 0;
    const data = props.umkms.data || [];
    const categories = [...new Set(data.map(umkm => umkm.kategori_usaha))].length;
    return { total, categories };
});

const getFotoUrl = (path) => {
    return path ? `/storage/${path.replace('public/', '')}` : 'https://placehold.co/400x300/e2e8f0/64748b?text=Tidak+Ada+Foto';
};

const getFotoPendukungUrls = (fotoPendukung) => {
    if (!fotoPendukung || typeof fotoPendukung !== 'string' || fotoPendukung.trim() === '') {
        return [];
    }
    try {
        const parsed = JSON.parse(fotoPendukung);
        return Array.isArray(parsed) ? parsed.map(path => `/storage/${path.replace('public/', '')}`).slice(0, 3) : [];
    } catch (e) {
        return [];
    }
};

const formatDate = (dateString) => {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Jakarta',
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const handleSearch = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) params.append('search', searchQuery.value);
    if (selectedKategori.value && selectedKategori.value !== 'Semua Kategori') {
        params.append('kategori', selectedKategori.value);
    }
    
    const queryString = params.toString();
    const url = queryString ? `${route('umkm.public.index')}?${queryString}` : route('umkm.public.index');
    window.location.href = url;
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedKategori.value = '';
    window.location.href = route('umkm.public.index');
};
</script>

<template>
    <Head title="Direktori UMKM Desa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="text-center">
                <h2 class="font-bold text-3xl text-gray-800 leading-tight">Direktori UMKM Desa</h2>
                <p class="text-lg text-gray-600 mt-2">Temukan dan dukung usaha lokal di desa kita</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Search and Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
                    <div class="flex flex-col lg:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Cari UMKM
                            </label>
                            <input
                                id="search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="Masukkan nama usaha atau pemilik..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="w-full lg:w-64">
                            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori
                            </label>
                            <select
                                id="kategori"
                                v-model="selectedKategori"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option v-for="kategori in kategoriUsaha" :key="kategori" :value="kategori === 'Semua Kategori' ? '' : kategori">
                                    {{ kategori }}
                                </option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                @click="handleSearch"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <button
                                @click="resetFilters"
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total UMKM Terdaftar</p>
                                <p class="text-3xl font-bold">{{ stats.total }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Kategori Usaha</p>
                                <p class="text-3xl font-bold">{{ stats.categories }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UMKM Grid -->
                <div v-if="umkms.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        v-for="umkm in umkms.data"
                        :key="umkm.id"
                        class="bg-white rounded-xl shadow-sm border hover:shadow-lg transition-all duration-300 overflow-hidden group"
                    >
                        <div class="relative">
                            <img
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                :src="getFotoUrl(umkm.foto_produk)"
                                :alt="`Foto ${umkm.nama_usaha}`"
                                @error="$event.target.src = 'https://placehold.co/400x300/e2e8f0/64748b?text=Tidak+Ada+Foto'"
                            />
                            <div class="absolute top-3 right-3">
                                <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                                    {{ umkm.kategori_usaha }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-1">
                                {{ umkm.nama_usaha }}
                            </h3>
                            
                            <div class="text-sm text-gray-600 mb-3 space-y-1">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="line-clamp-1">{{ umkm.nama_pemilik }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="line-clamp-1">{{ umkm.alamat_usaha }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ umkm.nomor_telepon }}</span>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-700 mb-4 line-clamp-2">
                                {{ umkm.deskripsi }}
                            </p>

                            <!-- Foto Pendukung -->
                            <div v-if="getFotoPendukungUrls(umkm.foto_pendukung).length" class="flex space-x-1 mb-4">
                                <img
                                    v-for="foto in getFotoPendukungUrls(umkm.foto_pendukung)"
                                    :key="foto"
                                    :src="foto"
                                    class="w-12 h-12 object-cover rounded-md border border-gray-200"
                                    @error="$event.target.src = 'https://placehold.co/100x100/e2e8f0/64748b?text=X'"
                                />
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <span class="text-xs text-gray-500">
                                    Terdaftar {{ formatDate(umkm.created_at) }}
                                </span>
                                <a
                                    :href="`tel:${umkm.nomor_telepon}`"
                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full hover:bg-green-700 transition-colors"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Hubungi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-6 text-xl font-medium text-gray-700">Tidak Ada UMKM Ditemukan</h3>
                    <p class="mt-2 text-gray-500 max-w-md mx-auto">
                        {{ search || kategori ? 'Coba ubah kata kunci pencarian atau filter yang digunakan.' : 'Belum ada UMKM yang terdaftar saat ini.' }}
                    </p>
                    <div v-if="search || kategori" class="mt-6">
                        <button
                            @click="resetFilters"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Lihat Semua UMKM
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="umkms.data.length > 0 && umkms.links" class="mt-8">
                    <nav class="flex items-center justify-center space-x-1">
                        <template v-for="(link, index) in umkms.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-2 text-sm font-medium rounded-md transition-colors"
                                :class="link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50'"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="px-3 py-2 text-sm font-medium text-gray-400 rounded-md"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>