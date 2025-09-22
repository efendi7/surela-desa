<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    profilDesa: {
        type: Object,
        default: () => ({
            nama_desa: 'Nama Desa',
            logo: null,
        }),
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head :title="`Log in - ${profilDesa.nama_desa}`" />

    <div class="min-h-screen flex flex-col lg:flex-row bg-gray-50 overflow-hidden">
        <!-- Desktop Background Image -->
        <div class="hidden lg:block lg:w-1/2 relative">
            <img
                src="/images/village-bg.jpg"
                alt="Pemandangan Desa"
                class="w-full h-full object-cover"
                onerror="this.src='https://images.unsplash.com/photo-1574263867128-a3d5c1b1dedc?q=80&w=2070&auto=format&fit=crop'"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-green-600/60 to-green-800/40"></div>
            <div class="absolute top-10 left-10 flex items-center space-x-3">
                <img
                    :src="profilDesa.logo ? `/storage/${profilDesa.logo}` : 'https://placehold.co/100x100/0F4C3A/FFFFFF?text=🏘️'"
                    :alt="profilDesa.nama_desa"
                    class="w-12 h-12 rounded-full border-2 border-green-200 shadow-sm"
                />
                <div class="flex flex-col">
                    <span class="font-bold text-lg text-white">DigiDes {{ profilDesa.nama_desa }}</span>
                    <span class="text-sm text-green-200 font-medium">Sistem Pelayanan Digital</span>
                </div>
            </div>
            <div class="absolute bottom-10 left-10 text-white max-w-md">
                <h2 class="text-3xl font-bold mb-4" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
                    Selamat Datang di {{ profilDesa.nama_desa }}
                </h2>
                <p class="text-lg leading-relaxed" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);">
                    Masuk untuk mengakses layanan desa modern, cepat, dan terpercaya.
                </p>
            </div>
        </div>

        <!-- Mobile Header Background -->
        <div class="lg:hidden relative h-48 sm:h-56">
            <img
                src="/images/village-bg.jpg"
                alt="Pemandangan Desa"
                class="w-full h-full object-cover"
                onerror="this.src='https://images.unsplash.com/photo-1574263867128-a3d5c1b1dedc?q=80&w=2070&auto=format&fit=crop'"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-green-600/70 to-green-800/50"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white px-4">
                <img
                    :src="profilDesa.logo ? `/storage/${profilDesa.logo}` : 'https://placehold.co/100x100/0F4C3A/FFFFFF?text=🏘️'"
                    :alt="profilDesa.nama_desa"
                    class="w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-green-200 shadow-sm mb-2"
                />
                <h1 class="font-bold text-lg sm:text-xl text-center">{{ profilDesa.nama_desa }}</h1>
                <p class="text-xs sm:text-sm text-green-200 font-medium text-center">Sistem Pelayanan Digital</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="flex-1 lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-12 -mt-24 lg:mt-0">
            <div class="w-full max-w-sm sm:max-w-md bg-white rounded-xl shadow-lg p-6 sm:p-8 my-auto">
                <!-- Desktop Logo (hidden on mobile) -->
                <div class="hidden lg:flex justify-center mb-6">
                    <img
                        :src="profilDesa.logo ? `/storage/${profilDesa.logo}` : 'https://placehold.co/100x100/0F4C3A/FFFFFF?text=🏘️'"
                        :alt="profilDesa.nama_desa"
                        class="w-16 h-16 rounded-full border-2 border-green-200 shadow-sm"
                    />
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 text-center mb-2">
                    Masuk ke DigiDes {{ profilDesa.nama_desa }}
                </h2>
                <p class="text-sm sm:text-base text-gray-600 text-center mb-6">
                    Akses layanan digital desa dengan mudah
                </p>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div class="animate-form-field">
                        <InputLabel for="email" value="Email" class="text-gray-700 font-semibold text-sm" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 text-sm sm:text-base py-2 sm:py-3"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2 text-xs sm:text-sm text-red-600" :message="form.errors.email" />
                    </div>

                    <div class="mt-4 animate-form-field" style="animation-delay: 0.1s;">
                        <InputLabel for="password" value="Password" class="text-gray-700 font-semibold text-sm" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 text-sm sm:text-base py-2 sm:py-3"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2 text-xs sm:text-sm text-red-600" :message="form.errors.password" />
                    </div>

                    <div class="mt-4 flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-xs sm:text-sm text-gray-600">Ingat saya</span>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-xs sm:text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-center sm:text-left"
                        >
                            Lupa kata sandi?
                        </Link>

                        <PrimaryButton
                            class="bg-green-600 text-white px-6 py-2 sm:py-3 rounded-lg hover:bg-green-700 transform hover:scale-105 transition-all duration-200 text-sm sm:text-base font-medium w-full sm:w-auto"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Masuk
                        </PrimaryButton>
                    </div>

                    <div class="mt-4 text-center">
                        <Link
                            :href="route('register')"
                            class="text-xs sm:text-sm text-green-600 underline hover:text-green-800"
                        >
                            Belum punya akun? Daftar sekarang
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animasi untuk elemen form agar muncul secara halus */
.animate-form-field {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(10px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Pastikan tidak ada celah putih */
html, body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Penyesuaian untuk mobile */
@media (max-width: 1023px) {
    .min-h-screen {
        min-height: 100vh;
        min-height: 100dvh; /* Dynamic viewport height untuk mobile */
    }
}

/* Penyesuaian untuk layar sangat kecil */
@media (max-width: 390px) {
    .rounded-xl {
        border-radius: 0.5rem;
    }
    
    .p-6 {
        padding: 1rem;
    }
}
</style>