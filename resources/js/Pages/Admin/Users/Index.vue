<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmUserModal from './Modals/ConfirmUserModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// --- PROPS ---
defineProps({
    users: Array,
});

// --- STATE MANAGEMENT ---
const flash = computed(() => usePage().props.flash);

// State untuk modal utama (Create, View, Edit)
const showModal = ref(false);
const modalMode = ref('create'); // Mode: 'create', 'view', 'edit'
const selectedUser = ref(null); // Menyimpan data pengguna yang dipilih

// State untuk modal konfirmasi (Bekukan/Aktifkan)
const showConfirmModal = ref(false);
const userToProcess = ref(null);
const confirmAction = ref('');

// State untuk preview foto
const photoPreview = ref(null);

const form = useForm({
    id: null,
    name: '', email: '', nik: '', phone: '', role: 'warga', address: '',
    tempat_lahir: '', tanggal_lahir: '', jenis_kelamin: 'Laki-laki', pekerjaan: '',
    agama: 'Islam', status_perkawinan: 'Belum Menikah', kewarganegaraan: 'Indonesia',
    password: '', password_confirmation: '',
    profile_photo: null, // Properti untuk menampung file foto
});

// --- COMPUTED PROPERTIES ---
const isFormDisabled = computed(() => modalMode.value === 'view');

const modalTitle = computed(() => {
    if (modalMode.value === 'create') return 'Tambah Pengguna Baru';
    if (modalMode.value === 'view') return 'Detail Pengguna';
    return 'Edit Data Pengguna';
});

const modalDescription = computed(() => {
    if (modalMode.value === 'create') return 'Isi detail lengkap pengguna yang akan ditambahkan.';
    if (modalMode.value === 'view') return 'Lihat detail pengguna. Klik edit untuk mengubah.';
    return 'Ubah detail informasi pengguna yang diperlukan.';
});


// --- MODAL & FORM FUNCTIONS ---
const openCreateModal = () => {
    modalMode.value = 'create';
    selectedUser.value = null;
    form.reset();
    showModal.value = true;
};

const openViewModal = (user) => {
    modalMode.value = 'view';
    selectedUser.value = { ...user };
    form.defaults({ ...user, password: '', password_confirmation: '' }).reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    photoPreview.value = null; // Reset preview saat modal ditutup
};

const updatePhotoPreview = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const createUser = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const updateUser = () => {
    // File uploads tidak bekerja dengan PUT, gunakan POST dengan method spoofing
    router.post(route('admin.users.update', selectedUser.value.id), {
        _method: 'put',
        ...form.data(), // Kirim semua data dari form
    }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
            if (form.errors.profile_photo) {
                form.reset('profile_photo');
            }
        },
    });
};

const switchToEditMode = () => {
    modalMode.value = 'edit';
};

const cancelEdit = () => {
    form.reset();
    photoPreview.value = null;
    modalMode.value = 'view';
};

// --- CONFIRM MODAL FUNCTIONS ---
const openConfirmModal = (user, action) => {
    userToProcess.value = user;
    confirmAction.value = action;
    showConfirmModal.value = true;
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
};

const handleConfirm = () => {
    const actionRoute = confirmAction.value === 'freeze'
        ? route('admin.users.destroy', userToProcess.value.id)
        : route('admin.users.restore', userToProcess.value.id);

    const method = confirmAction.value === 'freeze' ? 'delete' : 'post';

    router[method](actionRoute, {
        preserveScroll: true,
        onSuccess: () => closeConfirmModal(),
    });
};
</script>

<template>
    <Head title="Kelola Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Pengguna</h2>
        </template>

        <div class="py-4 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900">
                        <div class="mb-4">
                            <PrimaryButton @click="openCreateModal">Tambah Pengguna</PrimaryButton>
                        </div>

                        <div v-if="flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                            {{ flash.success }}
                        </div>
                        <div v-if="flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                            {{ flash.error }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-6 text-left">Pengguna</th>
                                        <th class="py-3 px-6 text-left">Role</th>
                                        <th class="py-3 px-6 text-center">Status</th>
                                        <th class="py-3 px-6 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b hover:bg-gray-50">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center space-x-4">
                                                <img :src="user.profile_photo_url" :alt="user.name" class="w-11 h-11 rounded-full object-cover border-2 border-gray-200">
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ user.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 capitalize">{{ user.role }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <span :class="user.is_frozen ? 'bg-gray-200 text-gray-800' : 'bg-green-100 text-green-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ user.is_frozen ? 'Dibekukan' : 'Aktif' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <button @click="openViewModal(user)" class="text-blue-600 hover:text-blue-900 mr-4 font-medium">Detail</button>
                                            <button v-if="!user.is_frozen" @click="openConfirmModal(user, 'freeze')" class="text-red-600 hover:text-red-900 font-medium">Bekukan</button>
                                            <button v-else @click="openConfirmModal(user, 'restore')" class="text-green-600 hover:text-green-900 font-medium">Aktifkan</button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">Belum ada data pengguna.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal" max-width="4xl">
            <div class="flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-200 rounded-t-lg">
                    <h2 class="text-lg font-medium text-gray-900">{{ modalTitle }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ modalDescription }}</p>
                </div>

                <div class="px-6 py-6 bg-gray-50 flex-grow overflow-y-auto">
                    <form @submit.prevent="modalMode === 'edit' ? updateUser() : createUser()" class="space-y-8">
                        <div class="bg-white p-6 rounded-lg shadow-sm border">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pribadi</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <InputLabel for="name" value="Nama Lengkap" />
                                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.name" />
                                </div>
                                <div>
                                    <InputLabel for="nik" value="NIK" />
                                    <TextInput id="nik" type="text" class="mt-1 block w-full" v-model="form.nik" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.nik" />
                                </div>
                                <div>
                                    <InputLabel for="phone" value="No. Telepon" />
                                    <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.phone" />
                                </div>
                                <div>
                                    <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                                    <TextInput id="tempat_lahir" type="text" class="mt-1 block w-full" v-model="form.tempat_lahir" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.tempat_lahir" />
                                </div>
                                <div>
                                    <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                                    <TextInput id="tanggal_lahir" type="date" class="mt-1 block w-full" v-model="form.tanggal_lahir" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_lahir" />
                                </div>
                                <div>
                                    <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                                    <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :disabled="isFormDisabled">
                                        <option>Laki-laki</option>
                                        <option>Perempuan</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.jenis_kelamin" />
                                </div>
                                <div>
                                    <InputLabel for="agama" value="Agama" />
                                    <select id="agama" v-model="form.agama" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :disabled="isFormDisabled">
                                        <option>Islam</option>
                                        <option>Kristen</option>
                                        <option>Katolik</option>
                                        <option>Hindu</option>
                                        <option>Buddha</option>
                                        <option>Khonghucu</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.agama" />
                                </div>
                                <div>
                                    <InputLabel for="status_perkawinan" value="Status Perkawinan" />
                                    <select id="status_perkawinan" v-model="form.status_perkawinan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :disabled="isFormDisabled">
                                        <option>Belum Menikah</option>
                                        <option>Menikah</option>
                                        <option>Cerai</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.status_perkawinan" />
                                </div>
                                <div>
                                    <InputLabel for="pekerjaan" value="Pekerjaan" />
                                    <TextInput id="pekerjaan" type="text" class="mt-1 block w-full" v-model="form.pekerjaan" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.pekerjaan" />
                                </div>
                                <div>
                                    <InputLabel for="kewarganegaraan" value="Kewarganegaraan" />
                                    <TextInput id="kewarganegaraan" type="text" class="mt-1 block w-full" v-model="form.kewarganegaraan" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.kewarganegaraan" />
                                </div>
                                <div class="md:col-span-2">
                                    <InputLabel for="address" value="Alamat Lengkap" />
                                    <textarea id="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm resize-none" v-model="form.address" :disabled="isFormDisabled"></textarea>
                                    <InputError class="mt-1" :message="form.errors.address" />
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-sm border">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <InputLabel for="profile_photo" value="Foto Profil (Opsional)" />
                                    <div v-if="modalMode !== 'create' && !photoPreview" class="mt-2">
                                        <img :src="selectedUser?.profile_photo_url" class="rounded-full h-20 w-20 object-cover">
                                    </div>
                                    <div v-if="photoPreview" class="mt-2">
                                        <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center" :style="'background-image: url(\'' + photoPreview + '\');'"></span>
                                    </div>
                                    <input v-if="modalMode !== 'view'" type="file" id="profile_photo" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" @input="form.profile_photo = $event.target.files[0]; updatePhotoPreview($event)">
                                    <InputError class="mt-1" :message="form.errors.profile_photo" />
                                </div>
                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" :disabled="isFormDisabled" />
                                    <InputError class="mt-1" :message="form.errors.email" />
                                </div>
                                <div>
                                    <InputLabel for="role" value="Role" />
                                    <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :disabled="isFormDisabled">
                                        <option value="warga">Warga</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.role" />
                                </div>
                                <template v-if="modalMode !== 'view'">
                                    <div>
                                        <InputLabel for="password" :value="modalMode === 'create' ? 'Password' : 'Password Baru (Opsional)'" />
                                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" />
                                        <InputError class="mt-1" :message="form.errors.password" />
                                    </div>
                                    <div>
                                        <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" />
                                    </div>
                                </template>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3 rounded-b-lg">
                    <template v-if="modalMode === 'create'">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton @click="createUser" :disabled="form.processing">Simpan</PrimaryButton>
                    </template>
                    <template v-else-if="modalMode === 'view'">
                        <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                        <PrimaryButton @click="switchToEditMode">Edit</PrimaryButton>
                    </template>
                    <template v-else-if="modalMode === 'edit'">
                        <SecondaryButton @click="cancelEdit">Batal</SecondaryButton>
                        <PrimaryButton @click="updateUser" :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                    </template>
                </div>
            </div>
        </Modal>

        <ConfirmUserModal
            :show="showConfirmModal"
            :user="userToProcess"
            :action="confirmAction"
            @close="closeConfirmModal"
            @confirm="handleConfirm"
        />
    </AuthenticatedLayout>
</template>