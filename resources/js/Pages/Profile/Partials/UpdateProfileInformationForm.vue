<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user; // Pastikan ini memuat semua kolom user
const photoInput = ref(null);
const photoPreview = ref(null);

// Form data dengan fallback kosong agar tidak undefined
const form = useForm({
    name: user?.name ?? '',
    email: user?.email ?? '',
    nik: user?.nik ?? '',
    phone: user?.phone ?? '',
    address: user?.address ?? '',
    tempat_lahir: user?.tempat_lahir ?? '',
    tanggal_lahir: user?.tanggal_lahir ?? '',
    jenis_kelamin: user?.jenis_kelamin ?? '',
    pekerjaan: user?.pekerjaan ?? '',
    agama: user?.agama ?? '',
    status_perkawinan: user?.status_perkawinan ?? '',
    kewarganegaraan: user?.kewarganegaraan ?? 'Indonesia',
    profile_photo: null,
});

// --- FOTO PROFIL ---
const selectNewPhoto = () => photoInput.value.click();

const updatePhotoPreview = () => {
    const photo = photoInput.value?.files[0];
    if (!photo) return;

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(photo.type)) {
        alert('File harus berupa gambar (JPEG, JPG, PNG, GIF)');
        return;
    }

    if (photo.size > 2 * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB');
        return;
    }

    form.profile_photo = photo;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    form.profile_photo = null;
    photoPreview.value = null;
    if (photoInput.value) photoInput.value.value = null;
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

// --- SUBMIT FORM ---
const submit = () => {
    if (form.profile_photo) {
        form.transform((data) => ({
            ...data,
            _method: 'patch',
        })).post(route('profile.update'), {
            errorBag: 'updateProfileInformation',
            preserveScroll: true,
            onSuccess: () => clearPhotoFileInput(),
        });
    } else {
        form.patch(route('profile.update'), {
            errorBag: 'updateProfileInformation',
            preserveScroll: true,
        });
    }
};
</script>

<template>
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil dan data pribadi akun Anda.
        </p>
    </header>

    <form @submit.prevent="submit" class="mt-6 space-y-6">
        <!-- Foto Profil -->
        <div>
            <InputLabel for="photo" value="Foto Profil" />

            <div class="mt-2" v-show="!photoPreview">
                <img
                    class="rounded-full h-20 w-20 object-cover"
                    :src="user.profile_photo_url"
                    :alt="user.name"
                >
            </div>

            <div class="mt-2" v-show="photoPreview">
                <span
                    class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                    :style="'background-image: url(' + photoPreview + ');'"
                ></span>
            </div>

            <input
                ref="photoInput"
                type="file"
                class="hidden"
                accept="image/*"
                @change="updatePhotoPreview"
            >

            <InputError :message="form.errors.profile_photo" class="mt-2" />

            <div class="flex items-center mt-2 space-x-2">
                <button
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500"
                    @click.prevent="selectNewPhoto"
                >
                    Pilih Foto Baru
                </button>

                <button
                    v-if="photoPreview"
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md text-xs font-semibold hover:bg-red-700"
                    @click.prevent="deletePhoto"
                >
                    Hapus Foto
                </button>
            </div>
        </div>

        <!-- Form Field -->
        <div>
            <InputLabel for="name" value="Nama Lengkap" />
            <TextInput id="name" type="text" class="mt-1 block w-full"
                v-model="form.name" required autocomplete="name" />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" class="mt-1 block w-full"
                v-model="form.email" required autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <!-- NIK & Phone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="nik" value="NIK" />
                <TextInput id="nik" type="text" class="mt-1 block w-full"
                    v-model="form.nik" />
                <InputError class="mt-2" :message="form.errors.nik" />
            </div>
            <div>
                <InputLabel for="phone" value="Nomor Telepon" />
                <TextInput id="phone" type="tel" class="mt-1 block w-full"
                    v-model="form.phone" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
        </div>

        <!-- Tempat & Tanggal Lahir -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                <TextInput id="tempat_lahir" type="text" class="mt-1 block w-full"
                    v-model="form.tempat_lahir" />
                <InputError class="mt-2" :message="form.errors.tempat_lahir" />
            </div>
            <div>
                <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                <TextInput id="tanggal_lahir" type="date" class="mt-1 block w-full"
                    v-model="form.tanggal_lahir" />
                <InputError class="mt-2" :message="form.errors.tanggal_lahir" />
            </div>
        </div>

        <!-- Jenis Kelamin -->
        <div>
            <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
            <select id="jenis_kelamin" v-model="form.jenis_kelamin"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="" disabled>Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
        </div>

        <!-- Alamat -->
        <div>
            <InputLabel for="address" value="Alamat" />
            <textarea id="address" rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                v-model="form.address"></textarea>
            <InputError class="mt-2" :message="form.errors.address" />
        </div>

        <!-- Pekerjaan -->
        <div>
            <InputLabel for="pekerjaan" value="Pekerjaan" />
            <TextInput id="pekerjaan" type="text" class="mt-1 block w-full"
                v-model="form.pekerjaan" />
            <InputError class="mt-2" :message="form.errors.pekerjaan" />
        </div>

        <!-- Agama -->
        <div>
            <InputLabel for="agama" value="Agama" />
            <select id="agama" v-model="form.agama"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="" disabled>Pilih Agama</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
            </select>
            <InputError class="mt-2" :message="form.errors.agama" />
        </div>

        <!-- Status Perkawinan -->
        <div>
            <InputLabel for="status_perkawinan" value="Status Perkawinan" />
            <select id="status_perkawinan" v-model="form.status_perkawinan"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="" disabled>Pilih Status</option>
                <option value="Belum Menikah">Belum Menikah</option>
                <option value="Menikah">Menikah</option>
                <option value="Cerai">Cerai</option>
            </select>
            <InputError class="mt-2" :message="form.errors.status_perkawinan" />
        </div>

        <!-- Kewarganegaraan -->
        <div>
            <InputLabel for="kewarganegaraan" value="Kewarganegaraan" />
            <TextInput id="kewarganegaraan" type="text" class="mt-1 block w-full"
                v-model="form.kewarganegaraan" />
            <InputError class="mt-2" :message="form.errors.kewarganegaraan" />
        </div>

        <!-- Verifikasi Email -->
        <div v-if="mustVerifyEmail && user.email_verified_at === null">
            <p class="mt-2 text-sm text-gray-800">
                Alamat email Anda belum terverifikasi.
                <Link :href="route('verification.send')" method="post" as="button"
                    class="text-sm text-gray-600 underline hover:text-gray-900">
                    Klik di sini untuk mengirim ulang email verifikasi.
                </Link>
            </p>
            <div v-show="status === 'verification-link-sent'"
                class="mt-2 text-sm font-medium text-green-600">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
            <Transition enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0">
                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                    Tersimpan.
                </p>
            </Transition>
        </div>
    </form>
</section>
</template>
