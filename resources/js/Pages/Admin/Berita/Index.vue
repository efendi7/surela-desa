<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import BeritaForm from './Partials/BeritaForm.vue';

const props = defineProps({
    // Prop name remains 'beritas' as it comes from the controller
    beritas: Object,
});

const showModal = ref(false);
// Use a more generic name like 'selectedPostingan'
const selectedPostingan = ref(null);

const openModal = (postingan = null) => {
    selectedPostingan.value = postingan;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedPostingan.value = null;
};

const deletePostingan = (postingan) => {
    // Use a more generic confirmation message
    if (confirm(`Apakah Anda yakin ingin menghapus postingan ini: "${postingan.title}"?`)) {
        useForm({}).delete(route('admin.berita.destroy', postingan.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <!-- Change title to be more generic -->
    <Head title="Manajemen Postingan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Postingan</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Change button text -->
                        <PrimaryButton @click="openModal()">Tambah Postingan</PrimaryButton>

                        <table class="min-w-full divide-y divide-gray-200 mt-6">
                            <thead>
                                <tr>
                                    <!-- Add a "Jenis" (Type) column -->
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Loop through the items, using a generic name 'postingan' -->
                                <tr v-for="postingan in beritas.data" :key="postingan.id">
                                    <!-- Display the type with a colored badge for clarity -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                postingan.type === 'berita' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                                            ]"
                                        >
                                            {{ postingan.type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ postingan.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ postingan.user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ new Date(postingan.published_at).toLocaleDateString('id-ID') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openModal(postingan)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <DangerButton @click="deletePostingan(postingan)" class="ml-2">Hapus</DangerButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    <!-- Dynamic modal title -->
                    {{ selectedPostingan ? `Edit ${selectedPostingan.type}` : 'Tambah Postingan Baru' }}
                </h2>
                <!-- Pass the selected item to the form -->
                <BeritaForm :berita="selectedPostingan" @close="closeModal" />
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
