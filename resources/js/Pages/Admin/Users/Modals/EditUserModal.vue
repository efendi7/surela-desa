<!-- resources/js/Pages/Admin/Users/Modals/EditUserModal.vue -->

<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    user: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '', email: '', nik: '', phone: '', role: '', address: '',
    tempat_lahir: '', tanggal_lahir: '', jenis_kelamin: '', pekerjaan: '',
    agama: '', status_perkawinan: '', kewarganegaraan: '',
    password: '', password_confirmation: '',
});

// Mengisi form ketika props.user berubah (saat modal dibuka)
watch(() => props.user, (newUser) => {
    if (newUser) {
        form.defaults({ ...newUser, password: '', password_confirmation: '' });
        form.reset();
    }
}, { immediate: true });


const closeModal = () => {
    // Tidak perlu reset form di sini karena watcher akan menanganinya
    emit('close');
};

const updateUser = () => {
    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
            // Fokus ke input pertama yang error jika ada
            const firstError = Object.keys(form.errors)[0];
            if (firstError) {
                document.getElementById(firstError)?.focus();
            }
        }
    });
};

// Membersihkan error saat modal ditampilkan
watch(() => props.show, (newVal) => {
    if (newVal) {
        form.clearErrors();
    }
});
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <!-- Wrapper untuk layout flexbox -->
        <div class="flex flex-col max-h-[90vh]">
            
            <!-- Header dengan gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 text-white rounded-t-lg">
                <h2 class="text-xl font-bold">Edit Data Pengguna</h2>
                <p class="text-blue-100 text-sm mt-1">Ubah informasi pengguna di bawah ini.</p>
            </div>

            <!-- Konten Form yang bisa di-scroll -->
            <div class="px-6 py-6 bg-gray-50 flex-grow overflow-y-auto">
                <form @submit.prevent="updateUser" class="space-y-8">
                    <!-- Data Pribadi Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <InputLabel for="name" value="Nama Lengkap" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" />
                                <InputError class="mt-1" :message="form.errors.name" />
                            </div>
                            <div>
                                <InputLabel for="nik" value="NIK" />
                                <TextInput id="nik" type="text" class="mt-1 block w-full" v-model="form.nik" maxlength="16" />
                                <InputError class="mt-1" :message="form.errors.nik" />
                            </div>
                            <div>
                                <InputLabel for="phone" value="No. Telepon" />
                                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" />
                                <InputError class="mt-1" :message="form.errors.phone" />
                            </div>
                            <div>
                                <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                                <TextInput id="tempat_lahir" type="text" class="mt-1 block w-full" v-model="form.tempat_lahir" />
                                <InputError class="mt-1" :message="form.errors.tempat_lahir" />
                            </div>
                            <div>
                                <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                                <TextInput id="tanggal_lahir" type="date" class="mt-1 block w-full" v-model="form.tanggal_lahir" />
                                <InputError class="mt-1" :message="form.errors.tanggal_lahir" />
                            </div>
                            <div>
                                <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                                <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.jenis_kelamin" />
                            </div>
                            <div>
                                <InputLabel for="agama" value="Agama" />
                                <select id="agama" v-model="form.agama" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option>Islam</option>
                                    <option>Kristen</option>
                                    <option>Katolik</option>
                                    <option>Hindu</option>
                                    <option>Buddha</option>
                                    <option>Khonghucu</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.agama" />
                            </div>
                            <div class="md:col-span-2">
                                <InputLabel for="address" value="Alamat Lengkap" />
                                <textarea id="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm resize-none" v-model="form.address"></textarea>
                                <InputError class="mt-1" :message="form.errors.address" />
                            </div>
                        </div>
                    </div>

                    <!-- Data Akun Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                                <InputError class="mt-1" :message="form.errors.email" />
                            </div>
                            <div>
                                <InputLabel for="role" value="Role" />
                                <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="warga">Warga</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.role" />
                            </div>
                            <div>
                                <InputLabel for="password" value="Password Baru (Opsional)" />
                                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" placeholder="Isi untuk mengubah password" />
                                <InputError class="mt-1" :message="form.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" value="Konfirmasi Password Baru" />
                                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Tetap -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 rounded-b-lg">
                <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                <PrimaryButton @click="updateUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
