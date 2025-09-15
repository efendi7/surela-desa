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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg p-4 shadow-sm border">
                            <h4 class="font-semibold text-gray-800 mb-2">Foto Bukti dari Warga</h4>
                            <a :href="pengaduan.foto_bukti_url" target="_blank">
                                <img :src="pengaduan.foto_bukti_url" alt="Foto Bukti" class="w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                        <div v-if="pengaduan.foto_proses_url" class="bg-white rounded-lg p-4 shadow-sm border">
                            <h4 class="font-semibold text-gray-800 mb-2">Foto Penanganan dari Admin</h4>
                             <a :href="pengaduan.foto_proses_url" target="_blank">
                                <img :src="pengaduan.foto_proses_url" alt="Foto Proses" class="w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                         <div v-else class="bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center text-center">
                            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
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