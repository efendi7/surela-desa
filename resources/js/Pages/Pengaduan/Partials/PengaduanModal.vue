<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePengaduanUtils } from '@/Composables/usePengaduanUtils';

const props = defineProps({
    show: Boolean,
    mode: String, // 'create' or 'view'
    pengaduan: Object,
});

const emit = defineEmits(['close', 'submit']);

const { formatDate, getStatusClass } = usePengaduanUtils();
const photoPreview = ref(null);

const form = useForm({
    judul: '',
    deskripsi: '',
    foto_bukti: null,
    alamat: '',
    kategori: '',
    latitude: '',
    longitude: '',
});

const modalTitle = computed(() => props.mode === 'create' ? 'Buat Laporan / Pengaduan Baru' : 'Detail Laporan / Pengaduan');

// Reset form dan preview saat modal dibuka untuk mode 'create'
watch(() => props.show, (newVal) => {
    if (newVal && props.mode === 'create') {
        form.reset();
        photoPreview.value = null;
    }
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    form.foto_bukti = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    emit('submit', form);
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="3xl">
        <div class="flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-900">{{ modalTitle }}</h2>
            </div>

            <div class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <form v-if="mode === 'create'" @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="judul" value="Judul Masalah" />
                        <TextInput id="judul" v-model="form.judul" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.judul" />
                    </div>
                    <div>
                        <InputLabel for="deskripsi" value="Deskripsi Lengkap Masalah" />
                        <TextArea id="deskripsi" v-model="form.deskripsi" class="mt-1 block w-full" rows="5" required />
                        <InputError class="mt-2" :message="form.errors.deskripsi" />
                    </div>
                    <div>
                        <InputLabel for="kategori" value="Kategori Masalah" />
                        <TextInput id="kategori" v-model="form.kategori" type="text" class="mt-1 block w-full" placeholder="cth: Infrastruktur, Kebersihan" />
                        <InputError class="mt-2" :message="form.errors.kategori" />
                    </div>
                    <div>
                        <InputLabel for="alamat" value="Alamat/Lokasi Kejadian (Opsional)" />
                        <TextArea id="alamat" v-model="form.alamat" class="mt-1 block w-full" rows="3" />
                        <InputError class="mt-2" :message="form.errors.alamat" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="latitude" value="Latitude (Opsional)" />
                            <TextInput id="latitude" v-model="form.latitude" type="text" class="mt-1 block w-full" placeholder="-7.00514" />
                            <InputError class="mt-2" :message="form.errors.latitude" />
                        </div>
                        <div>
                            <InputLabel for="longitude" value="Longitude (Opsional)" />
                            <TextInput id="longitude" v-model="form.longitude" type="text" class="mt-1 block w-full" placeholder="110.43812" />
                            <InputError class="mt-2" :message="form.errors.longitude" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="foto_bukti" value="Foto Bukti (Wajib)" />
                        <input id="foto_bukti" type="file" @change="handleFileChange" accept="image/jpeg,image/png,image/jpg" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer"/>
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                        <InputError class="mt-2" :message="form.errors.foto_bukti" />
                        <div v-if="photoPreview" class="mt-4 border rounded-lg p-2">
                            <p class="text-sm font-medium text-gray-600 mb-2">Preview:</p>
                            <img :src="photoPreview" class="w-full h-auto max-h-64 object-contain rounded" alt="Preview Foto Bukti">
                        </div>
                    </div>
                </form>

                <div v-if="mode === 'view' && pengaduan" class="space-y-6">
                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ pengaduan.judul }}</h3>
                                <p class="text-sm text-gray-500">Dilaporkan pada: {{ formatDate(pengaduan.created_at) }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize" :class="getStatusClass(pengaduan.status)">
                                {{ pengaduan.status }}
                            </span>
                        </div>
                        <p class="mt-4 text-gray-700 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
                        <div class="mt-4 pt-4 border-t grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h4 class="font-semibold text-gray-600">Kategori:</h4>
                                <p class="text-gray-800">{{ pengaduan.kategori || 'Tidak ada' }}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-600">Prioritas:</h4>
                                <p class="text-gray-800">{{ pengaduan.prioritas || 'Belum ditentukan' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <h4 class="font-semibold text-gray-600">Lokasi:</h4>
                                <p class="text-gray-800">{{ pengaduan.alamat || 'Tidak ada' }}</p>
                                <a v-if="pengaduan.latitude && pengaduan.longitude"
                                   :href="`https://www.google.com/maps/search/?api=1&query=${pengaduan.latitude},${pengaduan.longitude}`"
                                   target="_blank"
                                   class="text-indigo-600 hover:underline text-xs">
                                   Lihat di Google Maps
                                </a>
                            </div>
                            <!-- DIPERBAIKI: Menambahkan informasi estimasi selesai -->
                            <div v-if="pengaduan.estimasi_selesai" class="md:col-span-2">
                                <h4 class="font-semibold text-gray-600">Estimasi Selesai:</h4>
                                <p class="text-gray-800">{{ formatDate(pengaduan.estimasi_selesai) }}</p>
                            </div>
                            <div v-if="pengaduan.keterangan_admin" class="md:col-span-2 bg-blue-50 p-3 rounded-lg">
                                <h4 class="font-semibold text-gray-600">Keterangan dari Admin:</h4>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ pengaduan.keterangan_admin }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <h4 class="font-semibold text-gray-800 mb-2">Foto Bukti dari Warga</h4>
                            <a :href="pengaduan.foto_bukti_url" target="_blank">
                                <img :src="pengaduan.foto_bukti_url" alt="Foto Bukti" class="w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                        <!-- DIPERBAIKI: Foto penanganan sekarang akan muncul di modal warga -->
                        <div v-if="pengaduan.foto_proses_url" class="bg-white rounded-lg p-4 shadow-sm border">
                            <h4 class="font-semibold text-gray-800 mb-2">Foto Penanganan dari Admin</h4>
                            <a :href="pengaduan.foto_proses_url" target="_blank">
                                <img :src="pengaduan.foto_proses_url" alt="Foto Proses" class="w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                        <div v-else-if="mode === 'view'" class="bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center text-center">
                            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h4 class="font-semibold text-gray-700">Belum Ada Foto Penanganan</h4>
                            <p class="text-sm text-gray-500">Admin belum mengunggah foto proses penanganan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')">{{ mode === 'create' ? 'Batal' : 'Tutup' }}</SecondaryButton>
                <PrimaryButton v-if="mode === 'create'" @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ form.processing ? 'Mengirim...' : 'Kirim Laporan' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>