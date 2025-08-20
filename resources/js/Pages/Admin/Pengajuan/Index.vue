<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import { CheckCircleIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
    import { Head, useForm, usePage, router } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';

    const props = defineProps({
        pengajuanList: Object,
        filters: Object,
    });

    const pengajuanData = ref([...props.pengajuanList.data]);
    const showDetailModal = ref(false);
    const selectedPengajuan = ref(null);

    // Input file hidden
    const fileInputRef = ref(null);

    const form = useForm({
        status: '',
        keterangan_admin: '',
        nomor_surat: '',
    });

    const searchQuery = ref(props.filters?.search || '');
    const sortOrder = ref(props.filters?.sort_order || 'desc');
    const filterStatus = ref(props.filters?.status || '');
    const filterJenisSurat = ref(props.filters?.jenis_surat || '');

    const applyFilters = () => {
        router.get(
            route('admin.proses.index'),
            {
                search: searchQuery.value,
                sort_order: sortOrder.value,
                status: filterStatus.value,
                jenis_surat: filterJenisSurat.value,
            },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    let searchTimeout;
    watch(searchQuery, () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
    watch([sortOrder, filterStatus, filterJenisSurat], applyFilters);

    const openDetailModal = (pengajuan) => {
        selectedPengajuan.value = pengajuan;
        form.defaults({
            status: pengajuan.status,
            keterangan_admin: pengajuan.keterangan_admin || '',
            nomor_surat: pengajuan.nomor_surat || '',
        });
        form.reset();
        showDetailModal.value = true;
    };

    const closeModal = () => {
        showDetailModal.value = false;
        selectedPengajuan.value = null;
        form.reset();
    };

    const submitPerubahan = () => {
        if (!selectedPengajuan.value) return;

        form.patch(route('admin.proses.update', { pengajuanSurat: selectedPengajuan.value.id }), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    };

    // 🔹 Trigger upload file
    const triggerFileUpload = (pengajuan) => {
        selectedPengajuan.value = pengajuan;
        fileInputRef.value.click();
    };
    // 2. Fungsi yang menangani file setelah dipilih (logika baru)
    // 🔹 Tangani setelah file dipilih
    const handleFileSelected = (event) => {
        const file = event.target.files[0];
        if (!file || !selectedPengajuan.value) {
            event.target.value = null;
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        router.post(
            route('admin.proses.uploadFile', { pengajuan: selectedPengajuan.value.id }),
            formData,
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    // ✅ update di reactive array
                    const idx = pengajuanData.value.findIndex(
                        (p) => p.id === selectedPengajuan.value.id
                    );
                    if (idx !== -1) {
                        pengajuanData.value[idx] = {
                            ...pengajuanData.value[idx],
                            file_final: 'uploaded',
                        };
                    }
                    selectedPengajuan.value = null;
                },
                onError: (errors) => {
                    alert(Object.values(errors).join('\n'));
                },
            }
        );

        event.target.value = null; // reset input
    };
    // 3. Fungsi baru untuk konfirmasi (ikon centang)
    const konfirmasiSelesai = (pengajuan) => {
        if (
            !confirm(
                "Yakin ingin menyelesaikan pengajuan ini? Status akan diubah menjadi 'Selesai' dan file tidak bisa diubah lagi."
            )
        )
            return;

        router.post(route('admin.proses.konfirmasiFinal', { pengajuan: pengajuan.id }), {
            preserveScroll: true,
        });
    };

    // 4. Fungsi untuk hapus file (ikon tong sampah)
    const hapusFile = (pengajuan) => {
        if (!confirm('Yakin ingin menghapus file sementara ini?')) return;

        router.delete(route('admin.proses.hapusFile', { pengajuan: pengajuan.id }), {
            preserveScroll: true,
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proses Pengajuan Surat
            </h2>
        </template>

        <input
            type="file"
            ref="fileInputRef"
            @change="handleFileSelected"
            class="hidden"
            accept=".doc,.docx,.pdf"
        />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div
                            v-if="usePage().props.flash?.success"
                            class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                        >
                            {{ usePage().props.flash.success }}
                        </div>
                        <div
                            v-if="usePage().props.flash?.error"
                            class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
                        >
                            {{ usePage().props.flash.error }}
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari pemohon/nomor surat..."
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                />
                                <select
                                    v-model="filterStatus"
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                                <select
                                    v-model="filterJenisSurat"
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                >
                                    <option value="">Semua Jenis Surat</option>
                                </select>
                                <select
                                    v-model="sortOrder"
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                >
                                    <option value="desc">Terbaru</option>
                                    <option value="asc">Terlama</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-3 px-4 text-left font-semibold">Pemohon</th>
                                        <th class="py-3 px-4 text-left font-semibold">
                                            Jenis Surat
                                        </th>
                                        <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                                        <th class="py-3 px-4 text-center font-semibold">
                                            Template
                                        </th>
                                        <th class="py-3 px-4 text-center font-semibold">
                                            Upload & Konfirmasi
                                        </th>
                                        <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="pengajuan in pengajuanData"
                                        :key="pengajuan.id"
                                        class="border-b hover:bg-gray-50"
                                    >
                                        <td class="py-2 px-4">{{ pengajuan.user.name }}</td>
                                        <td class="py-2 px-4">
                                            {{ pengajuan.jenis_surat.nama_surat }}
                                        </td>
                                        <td class="py-2 px-4">
                                            {{
                                                new Date(pengajuan.created_at).toLocaleDateString(
                                                    'id-ID'
                                                )
                                            }}
                                        </td>
                                        <td class="py-2 px-4">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full capitalize"
                                                :class="getStatusClass(pengajuan.status)"
                                            >
                                                {{ pengajuan.status }}
                                            </span>
                                        </td>

                                        <td class="py-2 px-4 text-center">
                                            <a
                                                v-if="
                                                    ['diproses', 'selesai', 'ditolak'].includes(
                                                        pengajuan.status
                                                    ) && pengajuan.jenis_surat?.template_surat
                                                "
                                                :href="
                                                    route('admin.proses.downloadTemplate', {
                                                        pengajuan: pengajuan.id,
                                                    })
                                                "
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow transition"
                                            >
                                                Unduh
                                            </a>
                                            <span
                                                v-else-if="pengajuan.status === 'pending'"
                                                class="italic text-gray-500 text-xs"
                                            >
                                                Ubah status ke 'diproses'
                                            </span>
                                            <span v-else class="text-gray-400 text-sm">—</span>
                                        </td>

                                        <td class="py-2 px-4 text-center">
                                            <div
                                                v-if="
                                                    ['diproses', 'ditolak'].includes(
                                                        pengajuan.status
                                                    )
                                                "
                                            >
                                                <button
                                                    v-if="!pengajuan.file_final"
                                                    @click="triggerFileUpload(pengajuan)"
                                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md shadow transition"
                                                >
                                                    Upload File
                                                </button>

                                                <div
                                                    v-else
                                                    class="flex items-center justify-center space-x-3"
                                                >
                                                    <button
                                                        @click="konfirmasiSelesai(pengajuan)"
                                                        class="text-green-600 hover:text-green-800"
                                                        title="Konfirmasi & Selesaikan"
                                                    >
                                                        <CheckCircleIcon class="w-6 h-6" />
                                                    </button>
                                                    <button
                                                        @click="triggerFileUpload(pengajuan)"
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="Ganti file"
                                                    >
                                                        <PencilSquareIcon class="w-6 h-6" />
                                                    </button>
                                                    <button
                                                        @click="hapusFile(pengajuan)"
                                                        class="text-red-600 hover:text-red-800"
                                                        title="Hapus file sementara"
                                                    >
                                                        <TrashIcon class="w-6 h-6" />
                                                    </button>
                                                </div>
                                            </div>

                                            <div
                                                v-else-if="
                                                    pengajuan.status === 'selesai' &&
                                                    pengajuan.file_hasil
                                                "
                                            >
                                                <a
                                                    :href="
                                                        route('warga.pengajuan.download', {
                                                            pengajuan: pengajuan.id,
                                                        })
                                                    "
                                                    class="text-green-600 font-semibold hover:underline"
                                                >
                                                    Undah Surat Hasil
                                                </a>
                                            </div>

                                            <div v-else-if="pengajuan.status === 'pending'">
                                                <span class="italic text-gray-500 text-xs"
                                                    >Ubah status ke 'diproses'</span
                                                >
                                            </div>

                                            <div v-else>
                                                <span class="italic text-gray-400 text-sm">—</span>
                                            </div>
                                        </td>

                                        <td class="py-2 px-4 text-center">
                                            <button
                                                @click="openDetailModal(pengajuan)"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium"
                                            >
                                                Proses
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="pengajuanList.data.length === 0">
                                        <td colspan="7" class="text-center py-8 text-gray-500">
                                            Tidak ada data pengajuan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showDetailModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitPerubahan" v-if="selectedPengajuan">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-medium text-gray-900">
                        Detail: {{ selectedPengajuan.jenis_surat.nama_surat }}
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <p><strong>Pemohon:</strong> {{ selectedPengajuan.user.name }}</p>

                    <div>
                        <InputLabel for="nomor_surat" value="Nomor Surat" />
                        <input
                            v-model="form.nomor_surat"
                            id="nomor_surat"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="cth: 005/DS/I/2025"
                        />
                    </div>

                    <div>
                        <InputLabel for="status" value="Ubah Status" />
                        <select
                            v-model="form.status"
                            id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                            <option value="pending">Pending</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai" disabled>
                                Selesai (Otomatis via Konfirmasi)
                            </option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="keterangan_admin" value="Keterangan Admin (Opsional)" />
                        <textarea
                            v-model="form.keterangan_admin"
                            id="keterangan_admin"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        ></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                    <SecondaryButton type="button" @click="closeModal">Tutup</SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing"
                        >Simpan Perubahan</PrimaryButton
                    >
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
