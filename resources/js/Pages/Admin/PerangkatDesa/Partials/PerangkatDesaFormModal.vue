<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    isEdit: {
        type: Boolean,
        default: false
    },
    selectedPerangkat: {
        type: Object,
        default: null
    },
    jabatanOptions: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'submit']);

const form = useForm({
    nama: '',
    jabatan_desa_id: '',
    nik: '',
    nip: '',
    jenis_kelamin: 'L',
    tempat_lahir: '',
    tanggal_lahir: '',
    telepon: '',
    email: '',
    alamat: '',
    status_kepegawaian: 'Honorer',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status_jabatan: 'aktif',
    foto: null,
    pangkat_golongan: '',
    pendidikan_terakhir: '',
    dokumen: null,
    catatan: '',
});

// Watch for changes in selectedPerangkat to populate form data
watch(() => props.selectedPerangkat, (newPerangkat) => {
    if (newPerangkat && props.isEdit) {
        form.reset();
        form.clearErrors();
        
        Object.keys(form.data()).forEach(key => {
            if (key === 'foto' || key === 'dokumen') {
                form[key] = null;
            } else {
                form[key] = newPerangkat[key] || form.defaults()[key] || '';
            }
        });
    }
}, { immediate: true });

// Reset form when modal is closed
watch(() => props.show, (newShow) => {
    if (!newShow) {
        form.reset();
        form.clearErrors();
    }
});

const handleClose = () => {
    emit('close');
};

const handleSubmit = () => {
    const route = props.isEdit 
        ? window.route('admin.perangkat-desa.update', props.selectedPerangkat.id)
        : window.route('admin.perangkat-desa.store');
    
    if (props.isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route, {
            onSuccess: () => {
                emit('close');
            },
            onError: (errors) => {
                console.log('Validation errors:', errors);
            }
        });
    } else {
        form.post(route, {
            onSuccess: () => {
                emit('close');
            }
        });
    }
};
</script>

<template>
    <Modal :show="show" @close="handleClose" max-width="4xl">
        <div class="flex flex-col h-[90vh]">
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isEdit ? 'Edit Perangkat Desa' : 'Tambah Perangkat Desa' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tanda <span class="text-red-500 font-semibold">*</span> menunjukkan field wajib diisi
                </p>
            </div>
            
            <!-- Body Modal -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form @submit.prevent="handleSubmit" id="perangkat-form">
                    <div class="space-y-6">
                        <!-- Data Pribadi -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">1</span>
                                Data Pribadi
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nama" class="block text-sm font-medium text-gray-700">
                                        Nama Lengkap 
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        v-model="form.nama" 
                                        type="text" 
                                        id="nama" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Masukkan nama lengkap"
                                    >
                                    <div v-if="form.errors.nama" class="text-sm text-red-600 mt-1">{{ form.errors.nama }}</div>
                                </div>
                                
                                <div>
                                    <label for="nik" class="block text-sm font-medium text-gray-700">
                                        NIK 
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.nik" 
                                        type="text" 
                                        id="nik" 
                                        maxlength="16" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Contoh: 3374012345678901"
                                    >
                                    <div v-if="form.errors.nik" class="text-sm text-red-600 mt-1">{{ form.errors.nik }}</div>
                                    <p class="text-xs text-gray-500 mt-1">16 digit nomor identitas</p>
                                </div>
                                
                                <div>
                                    <label for="nip" class="block text-sm font-medium text-gray-700">
                                        NIP 
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.nip" 
                                        type="text" 
                                        id="nip" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Contoh: 197001011990011001"
                                    >
                                    <div v-if="form.errors.nip" class="text-sm text-red-600 mt-1">{{ form.errors.nip }}</div>
                                    <p class="text-xs text-gray-500 mt-1">Khusus untuk PNS/PPPK</p>
                                </div>
                                
                                <div>
                                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">
                                        Jenis Kelamin
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <select v-model="form.jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <div v-if="form.errors.jenis_kelamin" class="text-sm text-red-600 mt-1">{{ form.errors.jenis_kelamin }}</div>
                                </div>
                                
                                <div>
                                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">
                                        Tempat Lahir
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.tempat_lahir" 
                                        type="text" 
                                        id="tempat_lahir" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Contoh: Semarang"
                                    >
                                    <div v-if="form.errors.tempat_lahir" class="text-sm text-red-600 mt-1">{{ form.errors.tempat_lahir }}</div>
                                </div>
                                
                                <div>
                                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">
                                        Tanggal Lahir
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.tanggal_lahir" 
                                        type="date" 
                                        id="tanggal_lahir" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                    <div v-if="form.errors.tanggal_lahir" class="text-sm text-red-600 mt-1">{{ form.errors.tanggal_lahir }}</div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label for="alamat" class="block text-sm font-medium text-gray-700">
                                    Alamat
                                    <span class="text-gray-400 text-xs">(opsional)</span>
                                </label>
                                <textarea 
                                    v-model="form.alamat" 
                                    id="alamat" 
                                    rows="3" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Alamat lengkap tempat tinggal"
                                ></textarea>
                                <div v-if="form.errors.alamat" class="text-sm text-red-600 mt-1">{{ form.errors.alamat }}</div>
                            </div>
                        </div>
                        
                        <!-- Kontak -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full mr-2">2</span>
                                Informasi Kontak
                                <span class="text-gray-400 text-xs ml-2">(semua opsional)</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="telepon" class="block text-sm font-medium text-gray-700">
                                        Nomor Telepon
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.telepon" 
                                        type="tel" 
                                        id="telepon" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Contoh: 081234567890"
                                    >
                                    <div v-if="form.errors.telepon" class="text-sm text-red-600 mt-1">{{ form.errors.telepon }}</div>
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        id="email" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="contoh@email.com"
                                    >
                                    <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Jabatan & Kepegawaian -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full mr-2">3</span>
                                Jabatan & Kepegawaian
                                <span class="text-red-500 text-xs ml-2">*wajib diisi</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="jabatan" class="block text-sm font-medium text-gray-700">
                                        Jabatan 
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.jabatan_desa_id" id="jabatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option v-for="jabatan in jabatanOptions" :key="jabatan.id" :value="jabatan.id">{{ jabatan.nama_jabatan }}</option>
                                    </select>
                                    <div v-if="form.errors.jabatan_desa_id" class="text-sm text-red-600 mt-1">{{ form.errors.jabatan_desa_id }}</div>
                                </div>
                                
                                <div>
                                    <label for="status_kepegawaian" class="block text-sm font-medium text-gray-700">
                                        Status Kepegawaian 
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.status_kepegawaian" id="status_kepegawaian" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="PNS">PNS</option>
                                        <option value="PPPK">PPPK</option>
                                        <option value="Honorer">Honorer</option>
                                        <option value="Kontrak">Kontrak</option>
                                    </select>
                                    <div v-if="form.errors.status_kepegawaian" class="text-sm text-red-600 mt-1">{{ form.errors.status_kepegawaian }}</div>
                                </div>
                                
                                <div>
                                    <label for="pangkat_golongan" class="block text-sm font-medium text-gray-700">
                                        Pangkat/Golongan
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.pangkat_golongan" 
                                        type="text" 
                                        id="pangkat_golongan" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Contoh: III/a, IV/b"
                                    >
                                    <div v-if="form.errors.pangkat_golongan" class="text-sm text-red-600 mt-1">{{ form.errors.pangkat_golongan }}</div>
                                    <p class="text-xs text-gray-500 mt-1">Khusus untuk PNS/PPPK</p>
                                </div>
                                
                                <div>
                                    <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700">
                                        Pendidikan Terakhir
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <select v-model="form.pendidikan_terakhir" id="pendidikan_terakhir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA/SMK">SMA/SMK</option>
                                        <option value="D1">D1</option>
                                        <option value="D2">D2</option>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                    <div v-if="form.errors.pendidikan_terakhir" class="text-sm text-red-600 mt-1">{{ form.errors.pendidikan_terakhir }}</div>
                                </div>
                                
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">
                                        Tanggal Mulai Jabatan 
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        v-model="form.tanggal_mulai" 
                                        type="date" 
                                        id="tanggal_mulai" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                    <div v-if="form.errors.tanggal_mulai" class="text-sm text-red-600 mt-1">{{ form.errors.tanggal_mulai }}</div>
                                </div>
                                
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">
                                        Tanggal Selesai Jabatan
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        v-model="form.tanggal_selesai" 
                                        type="date" 
                                        id="tanggal_selesai" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                    <div v-if="form.errors.tanggal_selesai" class="text-sm text-red-600 mt-1">{{ form.errors.tanggal_selesai }}</div>
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika masih aktif dalam jabatan</p>
                                </div>
                                
                                <div>
                                    <label for="status_jabatan" class="block text-sm font-medium text-gray-700">
                                        Status Jabatan 
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.status_jabatan" id="status_jabatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="aktif">Aktif</option>
                                        <option value="non_aktif">Non-Aktif</option>
                                        <option value="cuti">Cuti</option>
                                        <option value="mutasi">Mutasi</option>
                                    </select>
                                    <div v-if="form.errors.status_jabatan" class="text-sm text-red-600 mt-1">{{ form.errors.status_jabatan }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File & Catatan -->
                        <div>
                            <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full mr-2">4</span>
                                File & Catatan
                                <span class="text-gray-400 text-xs ml-2">(semua opsional)</span>
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="foto" class="block text-sm font-medium text-gray-700">
                                        Foto Profil
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        @input="form.foto = $event.target.files[0]" 
                                        type="file" 
                                        id="foto" 
                                        accept="image/jpeg,image/jpg,image/png,image/webp" 
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    />
                                    <div v-if="form.errors.foto" class="text-sm text-red-600 mt-1">{{ form.errors.foto }}</div>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB</p>
                                </div>
                                
                                <div>
                                    <label for="dokumen" class="block text-sm font-medium text-gray-700">
                                        Dokumen Pendukung
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <input 
                                        @input="form.dokumen = Array.from($event.target.files)" 
                                        type="file" 
                                        id="dokumen" 
                                        multiple 
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    />
                                    <div v-if="form.errors.dokumen" class="text-sm text-red-600 mt-1">{{ form.errors.dokumen }}</div>
                                    <p class="text-xs text-gray-500 mt-1">SK pengangkatan, ijazah, dll. Format: PDF, DOC, DOCX, JPG, PNG. Maksimal 5MB per file</p>
                                </div>
                                
                                <div>
                                    <label for="catatan" class="block text-sm font-medium text-gray-700">
                                        Catatan Tambahan
                                        <span class="text-gray-400 text-xs">(opsional)</span>
                                    </label>
                                    <textarea 
                                        v-model="form.catatan" 
                                        id="catatan" 
                                        rows="3" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Informasi tambahan yang perlu dicatat..."
                                    ></textarea>
                                    <div v-if="form.errors.catatan" class="text-sm text-red-600 mt-1">{{ form.errors.catatan }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer Modal -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center flex-shrink-0 bg-gray-50">
                <div class="text-xs text-gray-500">
                    <span class="text-red-500">*</span> Field wajib diisi untuk menyimpan data
                </div>
                <div class="flex space-x-3">
                    <SecondaryButton @click="handleClose">
                        Batal
                    </SecondaryButton>
                    <PrimaryButton 
                        form="perangkat-form"
                        type="submit"
                        :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>