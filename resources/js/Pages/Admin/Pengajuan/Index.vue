<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    pengajuanList: Array,
});

const showDetailModal = ref(false);
const selectedPengajuan = ref(null);
const form = useForm({
    status: '',
    keterangan_admin: '',
});

const openDetailModal = (pengajuan) => {
    selectedPengajuan.value = pengajuan;
    form.status = pengajuan.status;
    form.keterangan_admin = pengajuan.keterangan_admin || '';
    showDetailModal.value = true;
};

const closeModal = () => {
    showDetailModal.value = false;
    selectedPengajuan.value = null;
    form.reset();
};

const updateStatus = () => {
    if (!selectedPengajuan.value) return;
    form.patch(route('admin.pengajuan.update', selectedPengajuan.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const getStatusClass = (status) => ({
    'bg-yellow-100 text-yellow-800': status === 'pending',
    'bg-blue-100 text-blue-800': status === 'diproses',
    'bg-green-100 text-green-800': status === 'selesai',
    'bg-red-100 text-red-800': status === 'ditolak',
});
</script>

<template>
    <Head title="Proses Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proses Pengajuan Surat</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="usePage().props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ usePage().props.flash.success }}
                        </div>
                         <div v-if="usePage().props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ usePage().props.flash.error }}
                        </div>

                        <!-- Tabel Pengajuan -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Pemohon</th>
                                        <th class="py-2 px-4 text-left">Jenis Surat</th>
                                        <th class="py-2 px-4 text-left">Tanggal</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pengajuan in pengajuanList" :key="pengajuan.id" class="border-b">
                                        <td class="py-2 px-4">{{ pengajuan.user.name }}</td>
                                        <td class="py-2 px-4">{{ pengajuan.jenis_surat.nama_surat }}</td>
                                        <td class="py-2 px-4">{{ new Date(pengajuan.created_at).toLocaleDateString('id-ID') }}</td>
                                        <td class="py-2 px-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full capitalize" :class="getStatusClass(pengajuan.status)">{{ pengajuan.status }}</span>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button @click="openDetailModal(pengajuan)" class="text-indigo-600 hover:text-indigo-900 mr-4">Proses</button>
                                            <!-- Gunakan <a> tag biasa untuk link eksternal atau file -->
                                            <a v-if="pengajuan.status === 'selesai'" :href="route('admin.pengajuan.cetak', pengajuan.id)" target="_blank" class="text-green-600 hover:text-green-900">
                                                Cetak
                                            </a>
                                            <span v-else class="text-gray-400 cursor-not-allowed">Cetak</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail & Proses -->
        <Modal :show="showDetailModal" @close="closeModal">
            <div class="p-6" v-if="selectedPengajuan">
                <h2 class="text-lg font-medium text-gray-900">Detail Pengajuan: {{ selectedPengajuan.jenis_surat.nama_surat }}</h2>
                
                <div class="mt-4 border-t pt-4 space-y-4 text-sm">
                    <!-- Data Pemohon, Lampiran, dll. -->
                    <p><strong>Pemohon:</strong> {{ selectedPengajuan.data_pemohon.nama }} (NIK: {{ selectedPengajuan.data_pemohon.nik }})</p>
                    <div>
                        <strong>Lampiran:</strong>
                        <ul class="list-disc list-inside ml-4">
                            <li v-for="(path, name) in selectedPengajuan.lampiran" :key="name">
                                <a :href="'/storage/' + path" target="_blank" class="text-indigo-600 hover:underline">{{ name.replace(/_/g, ' ') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="font-medium">Update Status Pengajuan</h3>
                    <div class="mt-4">
                        <InputLabel for="status" value="Status" />
                        <select v-model="form.status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="pending">Pending</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <InputLabel for="keterangan_admin" value="Keterangan / Alasan (jika ditolak)" />
                        <textarea v-model="form.keterangan_admin" id="keterangan_admin" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                    <PrimaryButton @click="updateStatus" class="ms-3" :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
