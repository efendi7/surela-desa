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

    const form = useForm({
        // PERBAIKAN: Baris '_method: 'PUT'' dihapus agar form mengirim sebagai POST murni,
        // sesuai dengan route di backend Anda.
        nama_desa: props.profilDesa.nama_desa || '',
        nama_kecamatan: props.profilDesa.nama_kecamatan || '',
        nama_kabupaten: props.profilDesa.nama_kabupaten || '',
        nama_provinsi: props.profilDesa.nama_provinsi || '',
        kode_pos: props.profilDesa.kode_pos || '',
        alamat: props.profilDesa.alamat || '',
        email: props.profilDesa.email || '',
        telepon: props.profilDesa.telepon || '',
        nama_kepala_desa: props.profilDesa.nama_kepala_desa || '',
        logo: null,
        sejarah: props.profilDesa.sejarah || '',
        visi: props.profilDesa.visi || '',
        misi: props.profilDesa.misi || '',
        struktur_organisasi: props.profilDesa.struktur_organisasi || [],
    });

    const flash = computed(() => usePage().props.flash);

    const openEditModal = () => {
        form.reset();
        form.struktur_organisasi = JSON.parse(
            JSON.stringify(props.profilDesa.struktur_organisasi || [])
        );
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
        // form.post akan mengirim request POST, yang sekarang sudah sesuai
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                >
                    {{ flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900">
                        <!-- Bagian Header Profil -->
                        <div class="flex flex-col sm:flex-row items-center mb-6 border-b pb-6">
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

                        <!-- Info Kontak & Administratif -->
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

                        <!-- Tampilan Struktur Organisasi di Halaman Admin -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-xl font-semibold mb-4">Struktur Organisasi</h3>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4"
                            >
                                <div
                                    v-for="perangkat in profilDesa.struktur_organisasi"
                                    :key="perangkat.jabatan"
                                >
                                    <p class="text-sm font-semibold text-gray-600">
                                        {{ perangkat.jabatan }}
                                    </p>
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

        <!-- Edit Modal -->
        <Modal :show="showEditModal" @close="closeModal" max-width="4xl">
            <form @submit.prevent="submitUpdate" class="flex flex-col max-h-[90vh]">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-medium text-gray-900">Edit Profil Desa</h2>
                </div>
                <div class="p-6 flex-1 overflow-y-auto space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="nama_desa" value="Nama Desa" />
                            <TextInput
                                id="nama_desa"
                                v-model="form.nama_desa"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.nama_desa" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="nama_kepala_desa" value="Nama Kepala Desa" />
                            <TextInput
                                id="nama_kepala_desa"
                                v-model="form.nama_kepala_desa"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.nama_kepala_desa" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="nama_kecamatan" value="Kecamatan" />
                            <TextInput
                                id="nama_kecamatan"
                                v-model="form.nama_kecamatan"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.nama_kecamatan" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="nama_kabupaten" value="Kabupaten" />
                            <TextInput
                                id="nama_kabupaten"
                                v-model="form.nama_kabupaten"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.nama_kabupaten" class="mt-2" />
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
                        <div class="md:col-span-2">
                            <InputLabel for="alamat" value="Alamat" />
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            ></textarea>
                            <InputError :message="form.errors.alamat" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
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

                    <div class="pt-6 border-t">
                        <div>
                            <InputLabel for="sejarah" value="Sejarah Desa" />
                            <textarea
                                id="sejarah"
                                v-model="form.sejarah"
                                rows="5"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            ></textarea>
                            <InputError :message="form.errors.sejarah" class="mt-2" />
                        </div>
                        <div class="mt-6">
                            <InputLabel for="visi" value="Visi" />
                            <textarea
                                id="visi"
                                v-model="form.visi"
                                rows="5"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            ></textarea>
                            <InputError :message="form.errors.visi" class="mt-2" />
                        </div>
                        <div class="mt-6">
                            <InputLabel for="misi" value="Misi" />
                            <textarea
                                id="misi"
                                v-model="form.misi"
                                rows="5"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            ></textarea>
                            <InputError :message="form.errors.misi" class="mt-2" />
                        </div>
                    </div>

                    <!-- Input untuk Struktur Organisasi -->
                    <div class="pt-6 border-t">
                        <h3 class="text-lg font-semibold mb-4">Struktur Organisasi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                v-for="(perangkat, index) in form.struktur_organisasi"
                                :key="index"
                            >
                                <InputLabel :for="'jabatan_' + index" :value="perangkat.jabatan" />
                                <TextInput
                                    :id="'jabatan_' + index"
                                    v-model="perangkat.nama"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :placeholder="'Nama ' + perangkat.jabatan"
                                />
                            </div>
                        </div>
                        <InputError :message="form.errors.struktur_organisasi" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end p-6 bg-gray-50 border-t">
                    <SecondaryButton @click="closeModal" type="button">Batal</SecondaryButton>
                    <PrimaryButton type="submit" class="ms-3" :disabled="form.processing"
                        >Simpan Perubahan</PrimaryButton
                    >
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
