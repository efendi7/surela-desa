<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    pengaduan: Object,
});

const emit = defineEmits(['close']);

const statusForm = useForm({
    status: '',
});

const prosesForm = useForm({
    foto_proses: null,
});

watch(() => props.pengaduan, (newVal) => {
    if (newVal) {
        statusForm.status = newVal.status;
    }
});

const updateStatus = () => {
    if (!props.pengaduan) return;
    statusForm.patch(route('admin.pengaduan.update.status', { pengaduan: props.pengaduan.id }), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const uploadProses = () => {
    if (!props.pengaduan) return;
    prosesForm.post(route('admin.pengaduan.upload.proses', { pengaduan: props.pengaduan.id }), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const handleFileChange = (event) => {
    prosesForm.foto_proses = event.target.files[0];
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="4xl">
        <div class="p-6" v-if="pengaduan">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Detail Pengaduan: {{ pengaduan.judul }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4 bg-gray-50 p-4 rounded-lg border">
                    <div>
                        <h3 class="font-semibold text-gray-600">Pelapor</h3>
                        <p class="text-gray-800">{{ pengaduan.user.name }} (NIK: {{ pengaduan.user.nik || 'N/A' }})</p>
                    </div>
                     <div>
                        <h3 class="font-semibold text-gray-600">Deskripsi Masalah</h3>
                        <p class="text-gray-800 whitespace-pre-wrap">{{ pengaduan.deskripsi }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-600 mb-2">Foto Bukti Warga</h3>
                        <a :href="pengaduan.foto_bukti_url" target="_blank">
                           <img :src="pengaduan.foto_bukti_url" class="rounded-lg w-full" alt="Foto Bukti">
                        </a>
                    </div>
                     <div v-if="pengaduan.foto_proses_url">
                        <h3 class="font-semibold text-gray-600 mb-2">Foto Bukti Penanganan</h3>
                        <a :href="pengaduan.foto_proses_url" target="_blank">
                           <img :src="pengaduan.foto_proses_url" class="rounded-lg w-full" alt="Foto Proses">
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-3">Ubah Status Laporan</h3>
                        <select v-model="statusForm.status" class="w-full border-gray-300 rounded-md">
                            <option value="Dikirim">Dikirim</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                         <PrimaryButton @click="updateStatus" :disabled="statusForm.processing" class="mt-3 w-full justify-center">
                            {{ statusForm.processing ? 'Menyimpan...' : 'Simpan Status' }}
                        </PrimaryButton>
                    </div>

                     <div class="bg-white p-4 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-3">Unggah Foto Penanganan</h3>
                        <input type="file" @change="handleFileChange" class="w-full text-sm"/>
                        <div v-if="prosesForm.errors.foto_proses" class="text-red-500 text-sm mt-1">{{ prosesForm.errors.foto_proses }}</div>
                         <PrimaryButton @click="uploadProses" :disabled="prosesForm.processing || !prosesForm.foto_proses" class="mt-3 w-full justify-center">
                           {{ prosesForm.processing ? 'Mengunggah...' : 'Unggah Foto' }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <SecondaryButton @click="$emit('close')">Tutup</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>