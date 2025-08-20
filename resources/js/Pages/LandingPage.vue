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
            title: 'Pengajuan Surat Online',
            description:
                'Ajukan surat domisili, usaha, dan lainnya tanpa perlu datang ke kantor desa.',
            icon: 'document-text',
            color: 'text-blue-400',
        },
        {
            title: 'Pengaduan Masyarakat',
            description:
                'Sampaikan aspirasi atau keluhan Anda langsung kepada perangkat desa secara transparan.',
            icon: 'chat-bubble-left-right',
            color: 'text-green-400',
        },
        {
            title: 'Pendaftaran UMK',
            description:
                'Daftarkan usaha mikro & kecil Anda untuk mendapatkan dukungan dari pemerintah.',
            icon: 'building-storefront',
            color: 'text-yellow-400',
        },
        {
            title: 'Informasi Desa',
            description:
                'Dapatkan pengumuman penting, berita, dan agenda kegiatan desa secara real-time.',
            icon: 'megaphone',
            color: 'text-pink-400',
        },
        {
            title: 'Transparansi Real-time',
            description:
                'Lacak status pengajuan dan proses layanan Anda secara langsung melalui sistem.',
            icon: 'chart-pie',
            color: 'text-purple-400',
        },
        {
            title: 'Profil Lengkap Desa',
            description:
                'Akses informasi mengenai sejarah, visi misi, dan struktur organisasi desa.',
            icon: 'building-library',
            color: 'text-indigo-400',
        },
    ];
</script>

<template>
    <Head title="Sistem Pelayanan Desa Digital" />

    <div class="font-sans antialiased text-gray-200">
        <!-- Floating Navigation -->
        <nav
            :class="`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${isScrolled ? 'bg-gray-900/95 backdrop-blur-lg shadow-2xl' : 'bg-transparent'}`"
        >
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img
                        :src="logoUrl"
                        :alt="profilDesa.nama_desa"
                        class="w-10 h-10 rounded-full ring-2 ring-blue-400/50"
                    />
                    <span class="font-bold text-xl text-white">{{ profilDesa.nama_desa }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="text-gray-300 hover:text-white transition-colors px-4 py-2 rounded-lg hover:bg-white/10"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full hover:scale-105 transform transition-all duration-300 shadow-lg"
                    >
                        Daftar
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Enhanced Hero Section -->
        <header
            class="relative w-full min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 overflow-hidden flex items-center justify-center text-center"
        >
            <!-- Animated background patterns -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse"
                ></div>
                <div
                    class="absolute top-3/4 right-1/4 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000"
                ></div>
                <div
                    class="absolute bottom-1/4 left-1/2 w-64 h-64 bg-pink-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000"
                ></div>
            </div>

            <!-- Background image with overlay -->
            <img
                src="/images/village-bg.jpg"
                alt="Desa"
                class="absolute inset-0 w-full h-full object-cover opacity-20"
            />
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"
            ></div>

            <!-- Hero content -->
            <div class="relative z-10 px-6 max-w-5xl mx-auto">
                <!-- Floating badge -->
                <div
                    class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-8 border border-white/20"
                >
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm text-white/90">Sistem Aktif 24/7</span>
                </div>

                <h1
                    class="text-6xl md:text-8xl font-extrabold mb-8 bg-gradient-to-r from-white via-blue-100 to-purple-200 bg-clip-text text-transparent leading-tight"
                >
                    Pelayanan Desa
                    <span
                        class="block bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
                        >Era Digital</span
                    >
                </h1>

                <p
                    class="text-xl md:text-2xl max-w-4xl mx-auto mb-12 text-gray-300 leading-relaxed"
                >
                    Transformasi digital untuk layanan administrasi yang lebih
                    <span class="text-blue-400 font-semibold">efisien</span>,
                    <span class="text-green-400 font-semibold">transparan</span>, dan
                    <span class="text-purple-400 font-semibold">mudah diakses</span>
                </p>

                <!-- Enhanced CTA buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('register')"
                        class="group relative inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold px-8 py-4 rounded-2xl shadow-2xl hover:shadow-blue-500/25 hover:scale-105 transform transition-all duration-300 overflow-hidden"
                    >
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></span>
                        <svg class="w-5 h-5 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414-1.414L9 5.586 7.707 4.293a1 1 0 00-1.414 1.414L8.586 8l-2.293 2.293a1 1 0 101.414 1.414L9 10.414l2.293 2.293a1 1 0 001.414-1.414L10.414 9l2.293-2.293z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                        <span class="relative z-10">Mulai Sekarang</span>
                    </Link>

                    <button
                        class="group inline-flex items-center gap-3 bg-white/10 backdrop-blur-md text-white font-semibold px-8 py-4 rounded-2xl border border-white/20 hover:bg-white/20 transition-all duration-300"
                    >
                        <svg
                            class="w-5 h-5 group-hover:animate-pulse"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                        Lihat Demo
                    </button>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white/60 rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </header>

        <!-- Enhanced Features Section -->
        <section
            class="py-20 sm:py-32 px-6 bg-gradient-to-b from-gray-900 via-slate-900 to-gray-900 relative overflow-hidden"
        >
            <!-- Background decorations -->
            <div class="absolute inset-0 opacity-5">
                <div
                    class="absolute top-0 left-0 w-72 h-72 bg-blue-500 rounded-full filter blur-3xl"
                ></div>
                <div
                    class="absolute bottom-0 right-0 w-72 h-72 bg-purple-500 rounded-full filter blur-3xl"
                ></div>
            </div>

            <div class="container mx-auto relative z-10">
                <!-- Section header -->
                <div class="text-center max-w-4xl mx-auto mb-20">
                    <div
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-md rounded-full px-6 py-3 mb-6 border border-blue-500/20"
                    >
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-300 font-medium">Fitur Unggulan</span>
                    </div>

                    <h2
                        class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent"
                    >
                        Semua Dalam Satu
                        <span
                            class="block text-transparent bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text"
                            >Platform</span
                        >
                    </h2>

                    <p class="text-xl text-gray-400 leading-relaxed">
                        Solusi komprehensif untuk menyederhanakan seluruh kebutuhan administrasi
                        desa Anda
                    </p>
                </div>

                <!-- Features grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    <FeatureCard
                        v-for="(feature, index) in features"
                        :key="feature.title"
                        :title="feature.title"
                        :description="feature.description"
                        :icon="feature.icon"
                        :color="feature.color"
                        :class="`hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-3 transition-all duration-500 animation-delay-${index * 100}`"
                        :style="`animation-delay: ${index * 0.1}s`"
                    />
                </div>

                <!-- Stats section -->
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-20 pt-20 border-t border-gray-800"
                >
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-400 mb-2">24/7</div>
                        <div class="text-gray-400">Layanan Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-400 mb-2">100%</div>
                        <div class="text-gray-400">Digital</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-purple-400 mb-2">5 Menit</div>
                        <div class="text-gray-400">Proses Cepat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-400 mb-2">Gratis</div>
                        <div class="text-gray-400">Untuk Semua</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced Call to Action -->
        <section
            class="py-20 px-6 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden"
        >
            <!-- Animated background -->
            <div class="absolute inset-0 opacity-20">
                <div
                    class="absolute top-1/3 left-1/4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-2xl animate-pulse"
                ></div>
                <div
                    class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-2xl animate-pulse animation-delay-2000"
                ></div>
            </div>

            <div class="container mx-auto max-w-5xl text-center relative z-10">
                <div
                    class="bg-white/5 backdrop-blur-xl p-12 md:p-16 rounded-3xl border border-white/10 shadow-2xl"
                >
                    <!-- Icon -->
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl mb-8"
                    >
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </div>

                    <h2
                        class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent"
                    >
                        Siap untuk Transformasi Digital?
                    </h2>

                    <p class="text-xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                        Bergabunglah bersama ribuan warga yang telah merasakan kemudahan layanan
                        desa digital. Mulai perjalanan Anda menuju masa depan yang lebih efisien.
                    </p>

                    <!-- Enhanced CTA -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="group relative inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold px-10 py-4 rounded-2xl shadow-2xl hover:shadow-blue-500/30 hover:scale-105 transform transition-all duration-300 overflow-hidden"
                        >
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></span>
                            <svg
                                class="w-6 h-6 relative z-10"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <span class="relative z-10">Buat Akun Gratis</span>
                        </Link>

                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            Data Anda Aman & Terlindungi
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced Footer -->
        <footer class="bg-gray-900 border-t border-gray-800 py-12">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Logo & Info -->
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <img
                                :src="logoUrl"
                                :alt="profilDesa.nama_desa"
                                class="w-12 h-12 rounded-full ring-2 ring-blue-400/30"
                            />
                            <span class="font-bold text-xl text-white">{{
                                profilDesa.nama_desa
                            }}</span>
                        </div>
                        <p class="text-gray-400 mb-4">{{ profilDesa.alamat }}</p>
                        <div class="flex space-x-4">
                            <a
                                href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-300"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-300"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-semibold text-white mb-4">Layanan</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li>
                                <a href="#" class="hover:text-white transition-colors"
                                    >Pengajuan Surat</a
                                >
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Pengaduan</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors"
                                    >Informasi Desa</a
                                >
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Bantuan</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="font-semibold text-white mb-4">Kontak</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                    ></path>
                                    <path
                                        d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                    ></path>
                                </svg>
                                info@desa.go.id
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                    ></path>
                                </svg>
                                (021) 1234-5678
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div
                    class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center"
                >
                    <p class="text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Pemerintah {{ profilDesa.nama_desa }}.
                        All rights reserved.
                    </p>
                    <p class="text-gray-500 text-sm mt-2 md:mt-0">
                        Ditenagai oleh Teknologi Modern
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
    @keyframes float {
        0%,
        100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .animation-delay-100 {
        animation-delay: 0.1s;
    }
    .animation-delay-200 {
        animation-delay: 0.2s;
    }
    .animation-delay-300 {
        animation-delay: 0.3s;
    }
    .animation-delay-400 {
        animation-delay: 0.4s;
    }
    .animation-delay-500 {
        animation-delay: 0.5s;
    }
    .animation-delay-600 {
        animation-delay: 0.6s;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }

    /* Custom gradient animations */
    @keyframes gradient-x {
        0%,
        100% {
            transform: translateX(0%);
        }
        50% {
            transform: translateX(100%);
        }
    }

    @keyframes pulse-glow {
        0%,
        100% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        50% {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.6);
        }
    }

    .animate-gradient-x {
        animation: gradient-x 15s ease infinite;
    }

    .animate-pulse-glow {
        animation: pulse-glow 3s ease-in-out infinite;
    }

    /* Glassmorphism effect */
    .backdrop-blur-xl {
        backdrop-filter: blur(20px);
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #1f2937;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #7c3aed);
    }
</style>
