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
                nama_desa: 'SURELA Desa',
                alamat: 'Alamat lengkap desa Anda.',
                email: 'email@desa.id',
                telepon: '081234567890',
                logo: null,
            }),
        },
    });

    const logoUrl = computed(() =>
        props.profilDesa?.logo
            ? `/storage/${props.profilDesa.logo}`
            : 'https://placehold.co/100x100/FFFFFF/1F2937?text=🏛️'
    );

    const isScrolled = ref(false);
    const handleScroll = () => (isScrolled.value = window.scrollY > 10);

    onMounted(() => window.addEventListener('scroll', handleScroll));
    onUnmounted(() => window.removeEventListener('scroll', handleScroll));

    const features = [
        {
            title: 'Surat Online',
            description:
                'Buat surat keterangan domisili, usaha, dan surat penting lainnya dari rumah.',
            icon: 'document-text',
            bgColor: 'bg-blue-50',
            iconColor: 'text-blue-600',
            borderColor: 'border-blue-200',
        },
        {
            title: 'Laporan Warga',
            description:
                'Sampaikan keluhan atau saran untuk kemajuan desa dengan mudah.',
            icon: 'chat-bubble-left-right',
            bgColor: 'bg-green-50',
            iconColor: 'text-green-600',
            borderColor: 'border-green-200',
        },
        {
            title: 'Daftar UMKM',
            description:
                'Daftarkan usaha kecil Anda untuk mendapat bantuan pemerintah.',
            icon: 'building-storefront',
            bgColor: 'bg-orange-50',
            iconColor: 'text-orange-600',
            borderColor: 'border-orange-200',
        },
        {
            title: 'Informasi Desa',
            description:
                'Dapatkan berita terbaru dan pengumuman penting dari desa.',
            icon: 'megaphone',
            bgColor: 'bg-purple-50',
            iconColor: 'text-purple-600',
            borderColor: 'border-purple-200',
        },
        {
            title: 'Lacak Status',
            description:
                'Pantau perkembangan pengajuan surat dan layanan Anda secara langsung.',
            icon: 'chart-pie',
            bgColor: 'bg-indigo-50',
            iconColor: 'text-indigo-600',
            borderColor: 'border-indigo-200',
        },
        {
            title: 'Profil Desa',
            description:
                'Pelajari sejarah, visi misi, dan struktur pemerintahan desa.',
            icon: 'building-library',
            bgColor: 'bg-teal-50',
            iconColor: 'text-teal-600',
            borderColor: 'border-teal-200',
        },
    ];
</script>

<template>
    <Head title="Pelayanan Desa Digital" />

    <div class="font-sans antialiased bg-white text-gray-900">
        <!-- Navigation -->
        <nav
            :class="`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${isScrolled ? 'bg-white/95 backdrop-blur-sm shadow-lg border-b border-gray-200' : 'bg-white/90 backdrop-blur-sm'}`"
        >
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img
                        :src="logoUrl"
                        :alt="profilDesa.nama_desa"
                        class="w-10 h-10 rounded-full border-2 border-gray-200"
                    />
                    <span class="font-bold text-xl text-gray-900">{{ profilDesa.nama_desa }}</span>
                </div>
                <div class="flex items-center space-x-4">
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
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section with Background Image -->
        <header class="relative pt-20 pb-16 overflow-hidden">
            <!-- Background Image dengan gradient overlay -->
            <div class="absolute inset-0 z-0">
                <img
                    src="/images/village-bg.jpg"
                    alt="Pemandangan Desa"
                    class="w-full h-full object-cover"
                />
                <!-- Overlay dengan gradient untuk fade effect -->
                <div class="absolute inset-0 bg-gradient-to-b from-white/40 via-white/60 to-white"></div>
            </div>

            <div class="relative z-10 container mx-auto px-6 text-center py-20">
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full mb-8 border border-green-200">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium">Layanan Aktif</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight hero-text-shadow">
                    Pelayanan Desa
                    <span class="text-blue-600 block">Lebih Mudah</span>
                </h1>

                <p class="text-xl md:text-2xl text-gray-700 max-w-3xl mx-auto mb-12 leading-relaxed hero-text-shadow">
                    Urus semua keperluan administrasi desa dari rumah. 
                    <span class="font-semibold text-gray-800">Praktis, cepat, dan transparan.</span>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-8 py-4 rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Mulai Sekarang
                    </Link>

                    <button class="inline-flex items-center gap-2 bg-white text-gray-700 font-semibold px-8 py-4 rounded-lg border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat Demo
                    </button>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center items-center gap-8 mt-16 pt-8 border-t border-white/30">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">24 Jam</div>
                        <div class="text-gray-600 text-sm">Layanan Online</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">Gratis</div>
                        <div class="text-gray-600 text-sm">Untuk Warga</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">5 Menit</div>
                        <div class="text-gray-600 text-sm">Proses Cepat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">Aman</div>
                        <div class="text-gray-600 text-sm">Data Terlindungi</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Features Section -->
        <section class="py-20 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-full mb-6 border border-blue-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Layanan Tersedia</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Semua Kebutuhan Desa
                        <span class="text-blue-600">Dalam Satu Tempat</span>
                    </h2>

                    <p class="text-lg text-gray-600">
                        Nikmati kemudahan mengurus berbagai keperluan administrasi desa tanpa harus datang ke kantor
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(feature, index) in features"
                        :key="feature.title"
                        :class="`${feature.bgColor} ${feature.borderColor} border-2 rounded-xl p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer`"
                        :style="`animation-delay: ${index * 0.1}s`"
                        class="animate-fade-in-up"
                    >
                        <!-- Icon -->
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
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24" v-else>
                                <path d="M11.584 2.376a.75.75 0 01.832 0l9 6a.75.75 0 11-.832 1.248L12 3.901 3.416 9.624a.75.75 0 01-.832-1.248l9-6z"></path>
                                <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 010 1.5H3a.75.75 0 010-1.5h.75v-9.918a.75.75 0 01.634-.74L12 8.251l7.616 1.343a.75.75 0 01.634.74zm-7.5 2.418a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zm3-2.25a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0V10.5zm3 0a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0V10.5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-6 text-center">
                <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-200 p-12">
                    <!-- Icon -->
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-600 rounded-xl mb-6">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Siap Merasakan Kemudahan?
                    </h2>

                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Bergabung dengan ribuan warga yang sudah merasakan kemudahan layanan desa digital. 
                        Daftar sekarang dan mulai urus keperluan Anda dari rumah.
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-8 py-4 rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Daftar Gratis Sekarang
                        </Link>

                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Data Anda Aman
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Logo & Info -->
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <img
                                :src="logoUrl"
                                :alt="profilDesa.nama_desa"
                                class="w-10 h-10 rounded-full border-2 border-gray-600"
                            />
                            <span class="font-bold text-xl">{{ profilDesa.nama_desa }}</span>
                        </div>
                        <p class="text-gray-400 mb-4">{{ profilDesa.alamat }}</p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-semibold mb-4">Layanan</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Pengajuan Surat</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Pengaduan</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Informasi Desa</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Bantuan</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="font-semibold mb-4">Kontak</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-center gap-2" v-if="profilDesa.email">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                {{ profilDesa.email }}
                            </li>
                            <li class="flex items-center gap-2" v-if="profilDesa.telepon">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                {{ profilDesa.telepon }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400">
                        &copy; {{ new Date().getFullYear() }} {{ profilDesa.nama_desa }}. Hak cipta dilindungi.
                    </p>
                    <p class="text-gray-500 text-sm mt-2 md:mt-0">
                        Sistem Digital Desa
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
        opacity: 0;
    }

    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }

    /* Custom hover effects */
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }

    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
    .hero-text-shadow {
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Dark shadow for contrast */
}
</style>