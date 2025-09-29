<!-- CreatePengaduanForm.vue -->
<script setup>
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    form: Object,
});

const photoPreview = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    props.form.foto_bukti = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};
</script>

<template>
    <div class="p-6 space-y-6">
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Pengaduan</h3>
                <p class="text-sm text-gray-600">Silakan lengkapi form di bawah ini untuk membuat laporan pengaduan.</p>
            </div>
            
            <div class="space-y-6">
                <div>
                    <InputLabel for="judul" value="Judul Masalah" />
                    <TextInput 
                        id="judul" 
                        v-model="form.judul" 
                        type="text" 
                        class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                        placeholder="Masukkan judul masalah yang ingin dilaporkan"
                        required 
                    />
                    <InputError class="mt-2" :message="form.errors.judul" />
                </div>
                
                <div>
                    <InputLabel for="deskripsi" value="Deskripsi Lengkap Masalah" />
                    <TextArea 
                        id="deskripsi" 
                        v-model="form.deskripsi" 
                        class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                        rows="5" 
                        placeholder="Jelaskan secara detail masalah yang Anda hadapi"
                        required 
                    />
                    <InputError class="mt-2" :message="form.errors.deskripsi" />
                </div>
                
                <div>
                    <InputLabel for="kategori" value="Kategori Masalah" />
                    <TextInput 
                        id="kategori" 
                        v-model="form.kategori" 
                        type="text" 
                        class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                        placeholder="cth: Infrastruktur, Kebersihan, Keamanan" 
                    />
                    <InputError class="mt-2" :message="form.errors.kategori" />
                </div>
                
                <div>
                    <InputLabel for="alamat" value="Alamat/Lokasi Kejadian" />
                    <TextArea 
                        id="alamat" 
                        v-model="form.alamat" 
                        class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                        rows="3" 
                        placeholder="Masukkan alamat atau lokasi kejadian (opsional)"
                    />
                    <InputError class="mt-2" :message="form.errors.alamat" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="latitude" value="Latitude (Opsional)" />
                        <TextInput 
                            id="latitude" 
                            v-model="form.latitude" 
                            type="text" 
                            class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                            placeholder="-7.00514" 
                        />
                        <InputError class="mt-2" :message="form.errors.latitude" />
                    </div>
                    <div>
                        <InputLabel for="longitude" value="Longitude (Opsional)" />
                        <TextInput 
                            id="longitude" 
                            v-model="form.longitude" 
                            type="text" 
                            class="mt-2 block w-full py-3 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" 
                            placeholder="110.43812" 
                        />
                        <InputError class="mt-2" :message="form.errors.longitude" />
                    </div>
                </div>
                
                <div>
                    <InputLabel for="foto_bukti" value="Foto Bukti (Wajib)" />
                    <input 
                        id="foto_bukti" 
                        type="file" 
                        @change="handleFileChange" 
                        accept="image/jpeg,image/png,image/jpg" 
                        class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer"
                        required
                    />
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                    <InputError class="mt-2" :message="form.errors.foto_bukti" />
                    
                    <div v-if="photoPreview" class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 mb-3">Preview Foto:</p>
                        <img :src="photoPreview" class="w-full h-auto max-h-64 object-contain rounded-lg shadow-sm" alt="Preview Foto Bukti">
                    </div>
                </div>
            </div>
        </div>

        <!-- Information -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-start">
                <svg class="flex-shrink-0 w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-base font-medium text-blue-900">Informasi Proses</h4>
                    <div class="mt-2 text-sm text-blue-800">
                        <p>Setelah pengaduan disubmit, laporan akan diverifikasi oleh admin. Anda akan mendapat notifikasi tentang status pengaduan melalui email.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>