<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    profilDesa: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);

const logoPreview = ref(null);

const form = useForm({
    nama_desa: '',
    nama_kecamatan: '',
    nama_kabupaten: '',
    nama_provinsi: '',
    kode_pos: '',
    alamat: '',
    email: '',
    telepon: '',
    nama_kepala_desa: '',
    logo: null,
    sejarah: '',
    visi: '',
    misi: '',
    struktur_organisasi: [],
});

// Gunakan watch untuk mengisi form saat modal ditampilkan
watch(() => props.show, (newVal) => {
    if (newVal) {
        form.nama_desa = props.profilDesa.nama_desa || '';
        form.nama_kecamatan = props.profilDesa.nama_kecamatan || '';
        form.nama_kabupaten = props.profilDesa.nama_kabupaten || '';
        form.nama_provinsi = props.profilDesa.nama_provinsi || '';
        form.kode_pos = props.profilDesa.kode_pos || '';
        form.alamat = props.profilDesa.alamat || '';
        form.email = props.profilDesa.email || '';
        form.telepon = props.profilDesa.telepon || '';
        form.nama_kepala_desa = props.profilDesa.nama_kepala_desa || '';
        form.sejarah = props.profilDesa.sejarah || '';
        form.visi = props.profilDesa.visi || '';
        form.misi = props.profilDesa.misi || '';
        form.struktur_organisasi = JSON.parse(JSON.stringify(props.profilDesa.struktur_organisasi || []));

        // Reset file input & preview
        logoPreview.value = null;
        form.logo = null;
        form.clearErrors();
    }
});


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
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="4xl">
        <form @submit.prevent="submitUpdate" class="flex flex-col max-h-[90vh]">
            <div class="p-6 border-b">
                <h2 class="text-lg font-medium text-gray-900">Edit Profil Desa</h2>
            </div>

            <div class="p-6 flex-1 overflow-y-auto space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="nama_desa" value="Nama Desa" />
                        <TextInput id="nama_desa" v-model="form.nama_desa" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.nama_desa" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="nama_kepala_desa" value="Nama Kepala Desa" />
                        <TextInput id="nama_kepala_desa" v-model="form.nama_kepala_desa" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nama_kepala_desa" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="nama_kecamatan" value="Kecamatan" />
                        <TextInput id="nama_kecamatan" v-model="form.nama_kecamatan" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nama_kecamatan" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="nama_kabupaten" value="Kabupaten" />
                        <TextInput id="nama_kabupaten" v-model="form.nama_kabupaten" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nama_kabupaten" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="nama_provinsi" value="Provinsi" />
                        <TextInput id="nama_provinsi" v-model="form.nama_provinsi" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nama_provinsi" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="kode_pos" value="Kode Pos" />
                        <TextInput id="kode_pos" v-model="form.kode_pos" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.kode_pos" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="telepon" value="Telepon" />
                        <TextInput id="telepon" v-model="form.telepon" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.telepon" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <InputLabel for="alamat" value="Alamat" />
                        <textarea id="alamat" v-model="form.alamat" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.alamat" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <InputLabel for="logo" value="Logo Desa (Kosongkan jika tidak diubah)" />
                        <input id="logo" @change="handleLogoChange" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <InputError :message="form.errors.logo" class="mt-2" />
                        <div v-if="logoPreview" class="mt-4">
                            <p class="text-sm font-medium">Pratinjau Logo Baru:</p>
                            <img :src="logoPreview" class="mt-2 h-20 w-20 rounded-full object-cover border" />
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t">
                    <div>
                        <InputLabel for="sejarah" value="Sejarah Desa" />
                        <textarea id="sejarah" v-model="form.sejarah" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.sejarah" class="mt-2" />
                    </div>
                    <div class="mt-6">
                        <InputLabel for="visi" value="Visi" />
                        <textarea id="visi" v-model="form.visi" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.visi" class="mt-2" />
                    </div>
                    <div class="mt-6">
                        <InputLabel for="misi" value="Misi" />
                        <textarea id="misi" v-model="form.misi" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.misi" class="mt-2" />
                    </div>
                </div>

                <div class="pt-6 border-t">
                    <h3 class="text-lg font-semibold mb-4">Struktur Organisasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="(perangkat, index) in form.struktur_organisasi" :key="index">
                            <InputLabel :for="'jabatan_' + index" :value="perangkat.jabatan" />
                            <TextInput :id="'jabatan_' + index" v-model="perangkat.nama" type="text" class="mt-1 block w-full" :placeholder="'Nama ' + perangkat.jabatan" />
                        </div>
                    </div>
                    <InputError :message="form.errors.struktur_organisasi" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end p-6 bg-gray-50 border-t">
                <SecondaryButton @click="$emit('close')" type="button">Batal</SecondaryButton>
                <PrimaryButton type="submit" class="ms-3" :disabled="form.processing">
                    Simpan Perubahan
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>