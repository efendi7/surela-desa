<!-- resources/js/Pages/Admin/Users/Modals/CreateUserModal.vue -->

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
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '', email: '', password: '', password_confirmation: '', nik: '', phone: '',
    role: 'warga', address: '', tempat_lahir: '', tanggal_lahir: '',
    jenis_kelamin: 'Laki-laki', pekerjaan: '', agama: 'Islam',
    status_perkawinan: 'Belum Menikah', kewarganegaraan: 'Indonesia',
});

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const createUser = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        form.reset();
        form.clearErrors();
    }
});
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="4xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Tambah Pengguna Baru</h2>
            <p class="mt-1 text-sm text-gray-600">Isi detail lengkap pengguna yang akan ditambahkan</p>
        </div>

        <!-- Content dengan scroll -->
        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto bg-gray-50">
            <form @submit.prevent="createUser" class="space-y-8">
                <!-- Data Pribadi Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Data Pribadi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <InputLabel for="name" value="Nama Lengkap" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="name" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.name" 
                                placeholder="Masukkan nama lengkap"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.name" />
                        </div>
                        
                        <div>
                            <InputLabel for="nik" value="NIK" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="nik" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.nik" 
                                placeholder="16 digit NIK"
                                maxlength="16"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.nik" />
                        </div>
                        
                        <div>
                            <InputLabel for="phone" value="No. Telepon" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="phone" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.phone" 
                                placeholder="Contoh: 08123456789"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.phone" />
                        </div>
                        
                        <div>
                            <InputLabel for="tempat_lahir" value="Tempat Lahir" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="tempat_lahir" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.tempat_lahir" 
                                placeholder="Kota tempat lahir"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.tempat_lahir" />
                        </div>
                        
                        <div>
                            <InputLabel for="tanggal_lahir" value="Tanggal Lahir" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="tanggal_lahir" 
                                type="date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.tanggal_lahir" 
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.tanggal_lahir" />
                        </div>
                        
                        <div>
                            <InputLabel for="jenis_kelamin" value="Jenis Kelamin" class="text-sm font-medium text-gray-700" />
                            <select 
                                id="jenis_kelamin" 
                                v-model="form.jenis_kelamin" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            >
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <InputError class="mt-1 text-sm" :message="form.errors.jenis_kelamin" />
                        </div>
                        
                        <div>
                            <InputLabel for="agama" value="Agama" class="text-sm font-medium text-gray-700" />
                            <select 
                                id="agama" 
                                v-model="form.agama" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            >
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Khonghucu">Khonghucu</option>
                            </select>
                            <InputError class="mt-1 text-sm" :message="form.errors.agama" />
                        </div>
                        
                        <div>
                            <InputLabel for="status_perkawinan" value="Status Perkawinan" class="text-sm font-medium text-gray-700" />
                            <select 
                                id="status_perkawinan" 
                                v-model="form.status_perkawinan" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            >
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah">Menikah</option>
                                <option value="Cerai">Cerai</option>
                            </select>
                            <InputError class="mt-1 text-sm" :message="form.errors.status_perkawinan" />
                        </div>
                        
                        <div>
                            <InputLabel for="pekerjaan" value="Pekerjaan" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="pekerjaan" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.pekerjaan" 
                                placeholder="Contoh: Karyawan Swasta"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.pekerjaan" />
                        </div>
                        
                        <div>
                            <InputLabel for="kewarganegaraan" value="Kewarganegaraan" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="kewarganegaraan" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.kewarganegaraan" 
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.kewarganegaraan" />
                        </div>
                        
                        <div class="md:col-span-2">
                            <InputLabel for="address" value="Alamat Lengkap" class="text-sm font-medium text-gray-700" />
                            <textarea 
                                id="address" 
                                rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors resize-none" 
                                v-model="form.address" 
                                placeholder="Alamat lengkap sesuai KTP"
                            ></textarea>
                            <InputError class="mt-1 text-sm" :message="form.errors.address" />
                        </div>
                    </div>
                </div>

                <!-- Data Akun Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Informasi Akun
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="email" value="Email" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="email" 
                                type="email" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.email" 
                                placeholder="contoh@email.com"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.email" />
                        </div>
                        
                        <div>
                            <InputLabel for="role" value="Role" class="text-sm font-medium text-gray-700" />
                            <select 
                                id="role" 
                                v-model="form.role" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                            >
                                <option value="warga">Warga</option>
                                <option value="admin">Admin</option>
                            </select>
                            <InputError class="mt-1 text-sm" :message="form.errors.role" />
                        </div>
                        
                        <div>
                            <InputLabel for="password" value="Password" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="password" 
                                type="password" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.password" 
                                placeholder="Minimal 8 karakter"
                            />
                            <InputError class="mt-1 text-sm" :message="form.errors.password" />
                        </div>
                        
                        <div>
                            <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-sm font-medium text-gray-700" />
                            <TextInput 
                                id="password_confirmation" 
                                type="password" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                v-model="form.password_confirmation" 
                                placeholder="Ulangi password"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-2">
            <SecondaryButton @click="closeModal">
                Batal
            </SecondaryButton>
            <PrimaryButton 
                @click="createUser" 
                :class="{ 'opacity-25': form.processing }" 
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ form.processing ? 'Menyimpan...' : 'Simpan Pengguna' }}
            </PrimaryButton>
        </div>
    </Modal>
</template>