<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import { Head, useForm, usePage } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        profilDesa: Object,
    });

    const showEditModal = ref(false);
    const logoPreview = ref(null);

    // Menyesuaikan form dengan nama kolom yang baru
    const form = useForm({
        _method: 'POST',
        nama_desa: props.profilDesa.nama_desa,
        nama_kecamatan: props.profilDesa.nama_kecamatan || '',
        nama_kabupaten: props.profilDesa.nama_kabupaten || '',
        nama_provinsi: props.profilDesa.nama_provinsi || '',
        kode_pos: props.profilDesa.kode_pos || '',
        alamat: props.profilDesa.alamat,
        email: props.profilDesa.email,
        telepon: props.profilDesa.telepon,
        nama_kepala_desa: props.profilDesa.nama_kepala_desa,
        logo: null,
        // Menghapus field yang tidak ada di model
        // visi: props.profilDesa.visi || '',
        // misi: props.profilDesa.misi || '',
        // struktur_organisasi: props.profilDesa.struktur_organisasi || '',
        // lembaga_desa: props.profilDesa.lembaga_desa || '',
        // demografi: props.profilDesa.demografi || '',
    });

    const flash = computed(() => usePage().props.flash);

    const openEditModal = () => {
        // Memastikan form di-reset dengan data terbaru dan nama kolom yang benar
        form.defaults({
            ...props.profilDesa,
            nama_kecamatan: props.profilDesa.nama_kecamatan || '',
            nama_kabupaten: props.profilDesa.nama_kabupaten || '',
            nama_provinsi: props.profilDesa.nama_provinsi || '',
            logo: null,
        }).reset();
        logoPreview.value = null;
        showEditModal.value = true;
    };

    const closeModal = () => {
        showEditModal.value = false;
    };

    const handleLogoChange = (event) => {
        const file = event.target.files[0];
        if (file) {
            form.logo = file;
            logoPreview.value = URL.createObjectURL(file);
        }
    };

    const submitUpdate = () => {
        form.post(route('admin.profil-desa.update'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
</script>

<template>
    <Head title="Profil Desa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil Desa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                >
                    {{ flash.success }}
                </div>

                <!-- Info Dasar Desa -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Informasi Dasar</h3>
                        <div class="flex flex-col sm:flex-row items-center mb-6">
                            <img
                                v-if="profilDesa.logo"
                                :src="'/storage/' + profilDesa.logo"
                                alt="Logo Desa"
                                class="w-24 h-24 rounded-full object-cover mb-4 sm:mb-0 sm:mr-6 border"
                            />
                            <img
                                v-else
                                src="https://placehold.co/100x100/e2e8f0/e2e8f0?text=Logo"
                                alt="Logo Desa"
                                class="w-24 h-24 rounded-full object-cover mb-4 sm:mb-0 sm:mr-6 border"
                            />

                            <div>
                                <h4 class="text-2xl font-bold">{{ profilDesa.nama_desa }}</h4>
                                <p class="text-gray-600">{{ profilDesa.alamat }}</p>
                                <!-- Menampilkan data dengan nama kolom yang benar -->
                                <div class="text-sm text-gray-500 mt-1">
                                    <span v-if="profilDesa.nama_kecamatan"
                                        >Kec. {{ profilDesa.nama_kecamatan }}</span
                                    >
                                    <span
                                        v-if="
                                            profilDesa.nama_kecamatan && profilDesa.nama_kabupaten
                                        "
                                    >
                                        |
                                    </span>
                                    <span v-if="profilDesa.nama_kabupaten"
                                        >Kab. {{ profilDesa.nama_kabupaten }}</span
                                    >
                                    <span
                                        v-if="profilDesa.nama_kabupaten && profilDesa.nama_provinsi"
                                    >
                                        |
                                    </span>
                                    <span v-if="profilDesa.nama_provinsi">{{
                                        profilDesa.nama_provinsi
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <strong class="block text-gray-500">Email</strong>
                                {{ profilDesa.email }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Telepon</strong>
                                {{ profilDesa.telepon }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Kecamatan</strong>
                                {{ profilDesa.nama_kecamatan || '-' }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Kabupaten</strong>
                                {{ profilDesa.nama_kabupaten || '-' }}
                            </div>
                            <div>
                                <strong class="block text-gray-500">Provinsi</strong>
                                {{ profilDesa.nama_provinsi || '-' }}
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

                            <div class="md:col-span-2">
                                <strong class="block text-gray-500">Nama Kepala Desa</strong>
                                {{ profilDesa.nama_kepala_desa }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menghapus bagian Visi, Misi, dll. karena tidak ada di model -->

                <div class="flex justify-end">
                    <PrimaryButton @click="openEditModal">Edit Profil Desa</PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" @close="closeModal" max-width="4xl">
            <div class="flex flex-col max-h-[90vh]">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-medium text-gray-900">Edit Profil Desa</h2>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="space-y-6">
                        <!-- Info Dasar -->
                        <div class="border-b pb-6">
                            <h3 class="text-md font-semibold mb-4">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="nama_desa" value="Nama Desa" />
                                    <TextInput
                                        id="nama_desa"
                                        v-model="form.nama_desa"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.nama_desa" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="nama_kecamatan" value="Kecamatan" />
                                    <TextInput
                                        id="nama_kecamatan"
                                        v-model="form.nama_kecamatan"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.nama_kecamatan"
                                        class="mt-2"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="nama_kabupaten" value="Kabupaten" />
                                    <TextInput
                                        id="nama_kabupaten"
                                        v-model="form.nama_kabupaten"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.nama_kabupaten"
                                        class="mt-2"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="nama_provinsi" value="Provinsi" />
                                    <TextInput
                                        id="nama_provinsi"
                                        v-model="form.nama_provinsi"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.nama_provinsi" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="kode_pos" value="Kode Pos" />
                                    <TextInput
                                        id="kode_pos"
                                        v-model="form.kode_pos"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.kode_pos" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="nama_kepala_desa" value="Nama Kepala Desa" />
                                    <TextInput
                                        id="nama_kepala_desa"
                                        v-model="form.nama_kepala_desa"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.nama_kepala_desa"
                                        class="mt-2"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="telepon" value="Telepon" />
                                    <TextInput
                                        id="telepon"
                                        v-model="form.telepon"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.telepon" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <InputLabel for="alamat" value="Alamat" />
                                <TextInput
                                    id="alamat"
                                    v-model="form.alamat"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.alamat" class="mt-2" />
                            </div>
                            <div class="mt-4">
                                <InputLabel
                                    for="logo"
                                    value="Logo Desa (Kosongkan jika tidak diubah)"
                                />
                                <input
                                    id="logo"
                                    @change="handleLogoChange"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <InputError :message="form.errors.logo" class="mt-2" />
                                <div v-if="logoPreview" class="mt-4">
                                    <p class="text-sm font-medium">Pratinjau Logo Baru:</p>
                                    <img
                                        :src="logoPreview"
                                        class="mt-2 h-20 w-20 rounded-full object-cover border"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end p-6 bg-gray-50 border-t">
                    <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                    <PrimaryButton @click="submitUpdate" class="ms-3" :disabled="form.processing"
                        >Simpan Perubahan</PrimaryButton
                    >
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
