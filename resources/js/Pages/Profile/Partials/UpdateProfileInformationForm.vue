<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

// Menambahkan semua field baru ke dalam form
const form = useForm({
    name: user.name,
    email: user.email,
    nik: user.nik || '',
    phone: user.phone || '',
    address: user.address || '',
    tempat_lahir: user.tempat_lahir || '',
    tanggal_lahir: user.tanggal_lahir || '',
    jenis_kelamin: user.jenis_kelamin || '',
    pekerjaan: user.pekerjaan || '',           // ✅
    agama: user.agama || '',                   // ✅
    status_perkawinan: user.status_perkawinan || '', // ✅
    kewarganegaraan: user.kewarganegaraan || 'Indonesia', // ✅
});

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Informasi Profil
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi profil dan data pribadi akun Anda.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            
            <!-- NIK and Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="nik" value="NIK" />
                    <TextInput id="nik" type="text" class="mt-1 block w-full" v-model="form.nik" autocomplete="nik" />
                    <InputError class="mt-2" :message="form.errors.nik" />
                </div>
                <div>
                    <InputLabel for="phone" value="Nomor Telepon" />
                    <TextInput id="phone" type="tel" class="mt-1 block w-full" v-model="form.phone" autocomplete="tel" />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
            </div>

            <!-- Tempat Lahir and Tanggal Lahir -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                    <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                    <TextInput id="tempat_lahir" type="text" class="mt-1 block w-full" v-model="form.tempat_lahir" />
                    <InputError class="mt-2" :message="form.errors.tempat_lahir" />
                </div>
                <div>
                    <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                    <TextInput id="tanggal_lahir" type="date" class="mt-1 block w-full" v-model="form.tanggal_lahir" />
                    <InputError class="mt-2" :message="form.errors.tanggal_lahir" />
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="" disabled>Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
            </div>

            <!-- Address -->
            <div>
                <InputLabel for="address" value="Alamat" />
                <textarea id="address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.address" rows="3"></textarea>
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <!-- Pekerjaan -->
<div>
    <InputLabel for="pekerjaan" value="Pekerjaan" />
    <TextInput id="pekerjaan" type="text" class="mt-1 block w-full" v-model="form.pekerjaan" />
    <InputError class="mt-2" :message="form.errors.pekerjaan" />
</div>

<!-- Agama -->
<div>
    <InputLabel for="agama" value="Agama" />
    <select id="agama" v-model="form.agama" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
    <select id="status_perkawinan" v-model="form.status_perkawinan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
    <TextInput id="kewarganegaraan" type="text" class="mt-1 block w-full" v-model="form.kewarganegaraan" />
    <InputError class="mt-2" :message="form.errors.kewarganegaraan" />
</div>



            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Alamat email Anda belum terverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
