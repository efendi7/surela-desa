<script setup>
    // PERUBAHAN: Menggunakan layout admin, bukan layout publik
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        pageTitle: String,
        content: [String, Array],
        contentType: String,
    });
</script>

<template>
    <Head :title="pageTitle" />

    <!-- PERUBAHAN: Menggunakan AuthenticatedLayout -->
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Tampilan kondisional berdasarkan contentType -->

                        <!-- Jika konten adalah HTML (untuk sejarah, visi-misi) -->
                        <div
                            v-if="contentType === 'html'"
                            class="prose max-w-none"
                            v-html="content"
                        ></div>

                        <!-- Jika konten adalah struktur organisasi (Array) -->
                        <div
                            v-if="contentType === 'struktur'"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <div
                                v-for="perangkat in content"
                                :key="perangkat.jabatan"
                                class="bg-gray-50 p-4 rounded-lg border"
                            >
                                <h3 class="font-bold text-lg text-gray-800">
                                    {{ perangkat.jabatan }}
                                </h3>
                                <p class="text-gray-600 mt-1">
                                    {{ perangkat.nama || 'Belum diisi' }}
                                </p>
                            </div>
                            <div
                                v-if="!content || content.length === 0"
                                class="col-span-full text-center text-gray-500"
                            >
                                Struktur organisasi belum diisi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    /* Styling dasar untuk konten dari v-html */
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .prose p {
        margin-bottom: 1rem;
    }
</style>
