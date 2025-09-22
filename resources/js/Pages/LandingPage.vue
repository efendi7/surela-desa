<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import FeatureCard from '@/Components/Landing/FeatureCard.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    profilDesa: {
        type: Object,
        default: () => ({
            nama_desa: 'Desa Kebumen',
            nama_kecamatan: 'Pringsurat',
            nama_kabupaten: 'Temanggung',
            alamat: 'Kecamatan Pringsurat, Kabupaten Temanggung, Jawa Tengah',
            email: 'desakebumen@temanggungkab.go.id',
            telepon: '(0293) 123456',
            logo: null,
            kode_pos: '56264',
            website: 'www.kebumen-pringsurat.desa.id'
        }),
    },
    statistics: {
        type: Array,
        default: () => [],
    },
});

const logoUrl = computed(() =>
    props.profilDesa?.logo
        ? `/storage/${props.profilDesa.logo}`
        : 'https://placehold.co/100x100/0F4C3A/FFFFFF?text=🏘️'
);

const isScrolled = ref(false);
const handleScroll = () => (isScrolled.value = window.scrollY > 10);

onMounted(() => window.addEventListener('scroll', handleScroll));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));

// Function to handle smooth scrolling
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Navigation items
const navItems = [
    { name: 'Fitur', sectionId: 'features-section' },
    { name: 'Program KKN', sectionId: 'kkn-section' },
    { name: 'Statistik', sectionId: 'statistics-section' },
    { name: 'Testimoni', sectionId: 'testimonials-section' },
    { name: 'Kontak', sectionId: 'contact-section' },
];

const features = [
    {
        title: 'Surat Keterangan Online',
        description:
            'Ajukan surat keterangan domisili, SKCK, surat kematian, dan surat penting lainnya tanpa antre.',
        icon: 'document-text',
        bgColor: 'bg-blue-50',
        iconColor: 'text-blue-600',
        borderColor: 'border-blue-200',
    },
    {
        title: 'Pengaduan Masyarakat',
        description:
            'Sampaikan keluhan infrastruktur, kebersihan, dan masalah desa lainnya dengan mudah.',
        icon: 'chat-bubble-left-right',
        bgColor: 'bg-green-50',
        iconColor: 'text-green-600',
        borderColor: 'border-green-200',
    },
    {
        title: 'Pendaftaran UMKM',
        description:
            'Daftarkan usaha pertanian, kerajinan, dan warung untuk bantuan modal usaha dari pemerintah.',
        icon: 'building-storefront',
        bgColor: 'bg-orange-50',
        iconColor: 'text-orange-600',
        borderColor: 'border-orange-200',
    },
    {
        title: 'Berita & Pengumuman',
        description:
            'Info terbaru tentang program desa, kegiatan gotong royong, dan pengumuman penting.',
        icon: 'megaphone',
        bgColor: 'bg-purple-50',
        iconColor: 'text-purple-600',
        borderColor: 'border-purple-200',
    },
    {
        title: 'Lacak Pengajuan',
        description:
            'Pantau status pengajuan surat dan bantuan sosial secara real-time melalui smartphone.',
        icon: 'chart-pie',
        bgColor: 'bg-indigo-50',
        iconColor: 'text-indigo-600',
        borderColor: 'border-indigo-200',
    },
    {
        title: 'Profil Desa Kebumen',
        description:
            'Pelajari potensi wisata, produk unggulan, sejarah, dan data demografis Desa Kebumen.',
        icon: 'building-library',
        bgColor: 'bg-teal-50',
        iconColor: 'text-teal-600',
        borderColor: 'border-teal-200',
    },
    {
        title: 'Program KKN',
        description:
            'Informasi kegiatan mahasiswa KKN, program pemberdayaan masyarakat, dan kolaborasi pendidikan.',
        icon: 'academic-cap',
        bgColor: 'bg-yellow-50',
        iconColor: 'text-yellow-600',
        borderColor: 'border-yellow-200',
    },
    {
        title: 'Potensi Desa',
        description:
            'Eksplorasi potensi pertanian, pariwisata, dan ekonomi kreatif untuk kemajuan bersama.',
        icon: 'chart-bar',
        bgColor: 'bg-red-50',
        iconColor: 'text-red-600',
        borderColor: 'border-red-200',
    },
];
</script>

<template>
    <Head :title="`Pelayanan Digital ${props.profilDesa.nama_desa}`" />

    <div class="font-sans antialiased bg-white text-gray-900">
        <!-- Navigation -->
        <nav
            :class="`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${isScrolled ? 'bg-white/95 backdrop-blur-sm shadow-lg border-b border-gray-200' : 'bg-white/90 backdrop-blur-sm'}`"
        >
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img
                        :src="logoUrl"
                        :alt="props.profilDesa.nama_desa"
                        class="w-10 h-10 rounded-full border-2 border-green-200 shadow-sm"
                    />
                    <div class="flex flex-col">
                        <span class="font-bold text-lg text-gray-900">{{ props.profilDesa.nama_desa }}</span>
                        <span class="text-xs text-green-600 font-medium">
                            {{ props.profilDesa.nama_kecamatan }} - {{ props.profilDesa.nama_kabupaten }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Navigation Links -->
                    <button
                        v-for="item in navItems"
                        :key="item.name"
                        @click="scrollToSection(item.sectionId)"
                        class="text-gray-600 hover:text-gray-900 transition-colors px-4 py-2 rounded-lg hover:bg-gray-100"
                    >
                        {{ item.name }}
                    </button>
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="text-gray-600 hover:text-gray-900 transition-colors px-4 py-2 rounded-lg hover:bg-gray-100"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        Daftar Warga
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section with Local Background -->
        <header class="relative pt-20 pb-16 overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img
                    src="/images/village-bg.jpg"
                    alt="Pemandangan Desa"
                    class="w-full h-full object-cover"
                    onerror="this.src='https://images.unsplash.com/photo-1574263867128-a3d5c1b1dedc?q=80&w=2070&auto=format&fit=crop'"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-white"></div>
            </div>

            <div class="relative z-10 container mx-auto px-6 text-center py-20">
                <div class="inline-flex items-center gap-2 bg-white/90 text-green-700 px-4 py-2 rounded-full mb-8 border border-green-200 shadow-lg backdrop-blur-sm">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium">Sistem Digital Aktif</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white leading-tight" style="text-shadow: 0 4px 12px rgba(0, 0, 0, 0.7), 0 2px 4px rgba(0, 0, 0, 0.8);">
                    DigiDes {{ props.profilDesa.nama_desa }}
                    <span class="text-green-400 block">Menuju Digital</span>
                </h1>

                <p class="text-xl md:text-2xl text-white max-w-3xl mx-auto mb-12 leading-relaxed" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7), 0 1px 3px rgba(0, 0, 0, 0.8);">
                    Wujudkan pelayanan desa yang modern dan efisien untuk masyarakat {{ props.profilDesa.nama_desa }}.
                    <span class="font-semibold text-green-300">Mudah, cepat, dan terpercaya.</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center gap-2 bg-green-600 text-white font-semibold px-8 py-4 rounded-lg shadow-lg hover:bg-green-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Daftar Sekarang
                    </Link>

                    <button class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm text-gray-800 font-semibold px-8 py-4 rounded-lg border-2 border-white/50 hover:bg-white transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                        </svg>
                        Panduan Penggunaan
                    </button>
                </div>

                <div class="flex flex-wrap justify-center items-center gap-4 mt-16 pt-8 border-t border-white/30">
                    <div class="text-center bg-black/20 backdrop-blur-sm rounded-lg px-4 py-3">
                        <div class="text-2xl font-bold text-green-400" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);">24/7</div>
                        <div class="text-white/90 text-sm" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);">Layanan Online</div>
                    </div>
                    <div class="text-center bg-black/20 backdrop-blur-sm rounded-lg px-4 py-3">
                        <div class="text-2xl font-bold text-blue-400" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);">Gratis</div>
                        <div class="text-white/90 text-sm" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);">Untuk Warga</div>
                    </div>
                    <div class="text-center bg-black/20 backdrop-blur-sm rounded-lg px-4 py-3">
                        <div class="text-2xl font-bold text-yellow-400" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);">KKN</div>
                        <div class="text-white/90 text-sm" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);">Kolaborasi</div>
                    </div>
                    <div v-if="statistics.length > 0" class="text-center bg-black/20 backdrop-blur-sm rounded-lg px-4 py-3">
                        <div class="text-2xl font-bold text-purple-400" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);">
                            {{ statistics[0].value }}
                        </div>
                        <div class="text-white/90 text-sm" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);">
                            {{ statistics[0].label }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Features Section -->
        <section id="features-section" class="py-20 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full mb-6 border border-green-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Program & Layanan</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Layanan Terpadu
                        <span class="text-green-600">{{ props.profilDesa.nama_desa }}</span>
                    </h2>

                    <p class="text-lg text-gray-600">
                        Nikmati kemudahan akses layanan desa modern yang didukung oleh program KKN dan teknologi digital
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        v-for="(feature, index) in features"
                        :key="feature.title"
                        :class="`${feature.bgColor} ${feature.borderColor} border-2 rounded-xl p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer`"
                        :style="`animation-delay: ${index * 0.1}s`"
                        class="animate-fade-in-up"
                    >
                        <div :class="`${feature.iconColor} mb-4`">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-if="feature.icon === 'document-text'">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V6.828L12.172 5H6v-.172L6 4zm2 8a1 1 0 100-2 1 1 0 000 2zm4-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'chat-bubble-left-right'">
                                <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 006 21.75a6.721 6.721 0 003.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 01-.814 1.686.75.75 0 00.44 1.173z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'building-storefront'">
                                <path d="M2.25 2.25a.75.75 0 000 1.5H3v10.5a3 3 0 003 3h1.21l-1.172 3.513a.75.75 0 001.424.474l.329-.987h8.418l.33.987a.75.75 0 001.422-.474L15.79 17.25H18a3 3 0 003-3V3.75h.75a.75.75 0 000-1.5H2.25zm6.04 16.5l.5-1.5h6.42l.5 1.5H8.29zm7.46-12a.75.75 0 00-1.5 0v6a.75.75 0 001.5 0v-6zm-3 2.25a.75.75 0 00-1.5 0v3.75a.75.75 0 001.5 0V9zm-3 2.25a.75.75 0 00-1.5 0v1.5a.75.75 0 001.5 0v-1.5z"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'megaphone'">
                                <path fill-rule="evenodd" d="M18.97 3.659a2.25 2.25 0 00-3.182.22l-1.94 2.28c-.783.923-1.923 1.52-3.153 1.52H8.25a2.25 2.25 0 00-2.25 2.25v3c0 1.242 1.008 2.25 2.25 2.25h2.445c1.23 0 2.37.597 3.153 1.52l1.94 2.28c.916 1.077 2.7.6 2.813-.75L21 12.25v-6.5l-2.03-2.091z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'chart-pie'">
                                <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'academic-cap'">
                                <path fill-rule="evenodd" d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v6.75a.75.75 0 01-1.5 0v-6.75a1.5 1.5 0 01.8-1.32l5.474-2.904c.023-.012.05-.02.072-.024a.75.75 0 01.54 0c.072.004.072.004.098.016l5.474 2.904a.75.75 0 01-.212 1.378 50.098 50.098 0 00-9.317-3.432.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else-if="feature.icon === 'chart-bar'">
                                <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                            </svg>
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else>
                                <path d="M11.584 2.376a.75.75 0 01.832 0l9 6a.75.75 0 11-.832 1.248L12 3.901 3.416 9.624a.75.75 0 01-.832-1.248l9-6z"></path>
                                <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 010 1.5H3a.75.75 0 010-1.5h.75v-9.918a.75.75 0 01.634-.74L12 8.251l7.616 1.343a.75.75 0 01.634.74zm-7.5 2.418a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zm3-2.25a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0V10.5zm3 0a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0V10.5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600 leading-relaxed text-sm">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Local Information Section (KKN Focus) -->
        <section id="kkn-section" class="py-20 bg-green-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Program KKN & Pemberdayaan Masyarakat
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Kolaborasi mahasiswa KKN dengan masyarakat {{ props.profilDesa.nama_desa }} untuk kemajuan bersama
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Posyandu Digital</h3>
                        <p class="text-gray-600">Program digitalisasi layanan kesehatan masyarakat dengan sistem pencatatan modern.</p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Bimbel Desa</h3>
                        <p class="text-gray-600">Program bimbingan belajar gratis untuk anak-anak desa dan pelatihan komputer untuk orang tua.</p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Market Online</h3>
                        <p class="text-gray-600">Platform pemasaran produk UMKM lokal dan pelatihan digital marketing untuk wirausaha desa.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section id="statistics-section" class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div v-for="stat in statistics" :key="stat.label" class="space-y-2">
                        <div :class="`text-3xl font-bold ${stat.color}`">{{ stat.value }}</div>
                        <div class="text-gray-600">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section id="testimonials-section" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Testimoni Warga {{ props.profilDesa.nama_desa }}
                    </h2>
                    <p class="text-lg text-gray-600">
                        Apa kata warga tentang layanan digital kami
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-semibold">BS</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Bapak Slamet</h4>
                                <p class="text-gray-600 text-sm">Petani</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                ★★★★★
                            </div>
                        </div>
                        <p class="text-gray-700">
                            "Sekarang buat surat keterangan tidak perlu ke kantor desa. Cukup dari rumah pakai HP sudah bisa. Sangat membantu."
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">IS</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Ibu Sari</h4>
                                <p class="text-gray-600 text-sm">Pedagang</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                ★★★★★
                            </div>
                        </div>
                        <p class="text-gray-700">
                            "Pendaftaran UMKM jadi mudah banget. Dapet bantuan modal usaha juga. Terima kasih program KKN!"
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-semibold">AR</span>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Ahmad Ridho</h4>
                                <p class="text-gray-600 text-sm">Mahasiswa</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="flex text-yellow-400">
                                ★★★★★
                            </div>
                        </div>
                        <p class="text-gray-700">
                            "Website ini sangat membantu program KKN kami. Warga jadi lebih mudah akses informasi dan layanan desa."
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact & Location Section -->
        <section id="contact-section" class="py-20 bg-green-600 text-white">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold mb-6">
                            Hubungi Kantor {{ props.profilDesa.nama_desa }}
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-200 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Alamat</h4>
                                    <p class="text-green-100">{{ props.profilDesa.alamat }}</p>
                                    <p class="text-green-100">Kode Pos: {{ props.profilDesa.kode_pos }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-200 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Telepon</h4>
                                    <p class="text-green-100">{{ props.profilDesa.telepon }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-200 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Email</h4>
                                    <p class="text-green-100">{{ props.profilDesa.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-200 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Website</h4>
                                    <p class="text-green-100">{{ props.profilDesa.website }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-semibold mb-4">Jam Pelayanan</h4>
                            <div class="space-y-2 text-green-100">
                                <p>Senin - Jumat: 08.00 - 15.00 WIB</p>
                                <p>Sabtu: 08.00 - 12.00 WIB</p>
                                <p class="text-sm">*Layanan online tersedia 24/7</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                        <h3 class="text-xl font-semibold mb-4">Peta Lokasi</h3>
                        <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                            <p class="text-gray-600">
                                [Peta {{ props.profilDesa.nama_desa }} akan dimuat di sini]
                            </p>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="#" class="inline-flex items-center gap-2 bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <img
                                :src="logoUrl"
                                :alt="props.profilDesa.nama_desa"
                                class="w-10 h-10 rounded-full"
                            />
                            <div>
                                <h3 class="text-xl font-bold">{{ props.profilDesa.nama_desa }}</h3>
                                <p class="text-gray-400 text-sm">Sistem Pelayanan Digital</p>
                            </div>
                        </div>
                        <p class="text-gray-300 mb-4 leading-relaxed">
                            Membangun desa yang modern dan terdepan melalui teknologi digital untuk pelayanan masyarakat yang lebih baik dan efisien.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.083.402-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.728-1.378 0 0-.594 2.267-.744 2.840-.282 1.084-1.064 2.456-1.549 3.235C9.584 23.815 10.77 24.001 12.017 24.001c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">Layanan</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li><a href="#" class="hover:text-white transition-colors">Surat Keterangan</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Pengaduan</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">UMKM</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Program KKN</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li>{{ props.profilDesa.telepon }}</li>
                            <li>{{ props.profilDesa.email }}</li>
                            <li>{{ props.profilDesa.website }}</li>
                            <li>Kode Pos: {{ props.profilDesa.kode_pos }}</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 {{ props.profilDesa.nama_desa }}. Dikembangkan dengan ❤️ oleh Tim KKN.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.hero-text-shadow {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .hero-text-shadow h1 {
        font-size: 2.5rem;
    }
    
    .grid {
        gap: 1rem;
    }
}
</style>