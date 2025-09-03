<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import EditProfilDesaModal from './Partials/EditProfilDesaModal.vue'; // <-- Import komponen modal baru

const props = defineProps({
    profilDesa: Object,
});

// State untuk mengontrol visibilitas modal
const showEditModal = ref(false);

const flash = computed(() => usePage().props.flash);

// Fungsi untuk membuka dan menutup modal
const openEditModal = () => {
    showEditModal.value = true;
};

const closeModal = () => {
    showEditModal.value = false;
};

// Computed property untuk menyederhanakan tampilan lokasi
const locationString = computed(() => {
    return [
        props.profilDesa.nama_kecamatan ? `Kec. ${props.profilDesa.nama_kecamatan}` : '',
        props.profilDesa.nama_kabupaten ? `Kab. ${props.profilDesa.nama_kabupaten}` : '',
        props.profilDesa.nama_provinsi || '',
    ]
    .filter(Boolean)
    .join(' | ');
});
</script>

<template>
    <Head title="Profil Desa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil Desa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                >
                    {{ flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900">
                        <div class="flex flex-col sm:flex-row items-center mb-6 border-b pb-6">
                            <img
                                :src="profilDesa.logo ? `/storage/${profilDesa.logo}` : 'https://placehold.co/100x100/e2e8f0/e2e8f0?text=Logo'"
                                alt="Logo Desa"
                                class="w-24 h-24 rounded-full object-cover mb-4 sm:mb-0 sm:mr-6 border"
                            />
                            <div>
                                <h4 class="text-2xl font-bold">{{ profilDesa.nama_desa }}</h4>
                                <p class="text-gray-600">{{ profilDesa.alamat }}</p>
                                <p v-if="locationString" class="text-sm text-gray-500 mt-1">{{ locationString }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <strong class="block text-gray-500">Nama Kepala Desa</strong>
                                {{ profilDesa.nama_kepala_desa || '-' }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Kode Pos</strong>
                                {{ profilDesa.kode_pos || '-' }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Email</strong>
                                {{ profilDesa.email || '-' }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Telepon</strong>
                                {{ profilDesa.telepon || '-' }}
                            </div>
                        </div>

                        <div v-if="profilDesa.struktur_organisasi?.length" class="mt-8 pt-6 border-t">
                            <h3 class="text-xl font-semibold mb-4">Struktur Organisasi</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                                <div v-for="perangkat in profilDesa.struktur_organisasi" :key="perangkat.jabatan">
                                    <p class="text-sm font-semibold text-gray-600">{{ perangkat.jabatan }}</p>
                                    <p class="text-lg">{{ perangkat.nama || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <PrimaryButton @click="openEditModal">Edit Profil Desa</PrimaryButton>
                </div>
            </div>
        </div>

        <EditProfilDesaModal
            :show="showEditModal"
            :profil-desa="profilDesa"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>