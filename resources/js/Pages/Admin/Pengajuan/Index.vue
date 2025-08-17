<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import Modal from '@/Components/Modal.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import { Head, useForm, usePage, router } from '@inertiajs/vue3';
    import { ref, computed, watch } from 'vue';

    const props = defineProps({
        pengajuanList: Array,
        filters: Object,
        pagination: Object,
    });

    const showDetailModal = ref(false);
    const showCetakModal = ref(false);
    const selectedPengajuan = ref(null);
    const selectedPengajuanCetak = ref(null);

    // Search and filter states
    const searchQuery = ref(props.filters?.search || '');
    const sortOrder = ref(props.filters?.sort_order || 'desc');
    const filterStatus = ref(props.filters?.status || '');
    const filterJenisSurat = ref(props.filters?.jenis_surat || '');

    const form = useForm({
        status: '',
        keterangan_admin: '',
        nomor_surat: '',
    });

    // Get unique jenis surat for filter dropdown
    const uniqueJenisSurat = computed(() => {
        const jenisSet = new Set();
        props.pengajuanList.forEach((item) => {
            jenisSet.add(item.jenis_surat.nama_surat);
        });
        return Array.from(jenisSet).sort();
    });

    // Apply filters
    const applyFilters = () => {
        router.get(
            route('admin.proses.index'),
            {
                search: searchQuery.value,
                sort_order: sortOrder.value,
                status: filterStatus.value,
                jenis_surat: filterJenisSurat.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    // Watch for changes and apply filters with debounce
    let searchTimeout;
    watch([searchQuery], () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });

    watch([sortOrder, filterStatus, filterJenisSurat], applyFilters);

    const openDetailModal = (pengajuan) => {
        selectedPengajuan.value = pengajuan;
        form.status = pengajuan.status;
        form.keterangan_admin = pengajuan.keterangan_admin || '';
        form.nomor_surat = pengajuan.nomor_surat || '';
        showDetailModal.value = true;
    };

    const openCetakModal = (pengajuan) => {
        selectedPengajuanCetak.value = pengajuan;
        showCetakModal.value = true;
    };

    const closeModal = () => {
        showDetailModal.value = false;
        selectedPengajuan.value = null;
        form.reset();
    };

    const closeCetakModal = () => {
        showCetakModal.value = false;
        selectedPengajuanCetak.value = null;
    };

    const updateStatus = () => {
        if (!selectedPengajuan.value) return;
        form.patch(route('admin.proses.update', selectedPengajuan.value.id), {
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

    // Pagination helper
    const goToPage = (url) => {
        if (url) {
            router.get(
                url,
                {
                    search: searchQuery.value,
                    sort_by: sortBy.value,
                    sort_order: sortOrder.value,
                    status: filterStatus.value,
                    jenis_surat: filterJenisSurat.value,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                }
            );
        }
    };
</script>

<template>
    <Head title="Proses Pengajuan Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proses Pengajuan Surat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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

                        <!-- Search and Filter Section -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                <!-- Search Input -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Cari Pemohon/Nomor Surat
                                    </label>
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Nama pemohon atau nomor surat..."
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Status
                                    </label>
                                    <select
                                        v-model="filterStatus"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </div>

                                <!-- Jenis Surat Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jenis Surat
                                    </label>
                                    <select
                                        v-model="filterJenisSurat"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Semua Jenis</option>
                                        <option
                                            v-for="jenis in uniqueJenisSurat"
                                            :key="jenis"
                                            :value="jenis"
                                        >
                                            {{ jenis }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Sort Options -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Urutkan Tanggal
                                    </label>
                                    <select
                                        v-model="sortOrder"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="desc">Terbaru</option>
                                        <option value="asc">Terlama</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Results Info -->
                        <div v-if="pagination" class="mb-4 text-sm text-gray-600">
                            Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
                            {{ pagination.total || 0 }} pengajuan
                        </div>

                        <!-- Tabel Pengajuan -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Pemohon</th>
                                        <th class="py-2 px-4 text-left">Nomor Surat</th>
                                        <th class="py-2 px-4 text-left">Jenis Surat</th>
                                        <th class="py-2 px-4 text-left">Tanggal</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="pengajuan in pengajuanList"
                                        :key="pengajuan.id"
                                        class="border-b hover:bg-gray-50"
                                    >
                                        <td class="py-2 px-4">{{ pengajuan.user.name }}</td>
                                        <td class="py-2 px-4">
                                            <span
                                                v-if="pengajuan.nomor_surat"
                                                class="text-sm font-mono bg-gray-100 px-2 py-1 rounded"
                                            >
                                                {{ pengajuan.nomor_surat }}
                                            </span>
                                            <span v-else class="text-gray-400 text-sm italic">
                                                Belum ada nomor
                                            </span>
                                        </td>
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
                                                >{{ pengajuan.status }}</span
                                            >
                                        </td>
                                        <td class="py-2 px-4">
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center gap-2"
                                            >
                                                <button
                                                    @click="openDetailModal(pengajuan)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Proses
                                                </button>

                                                <!-- Tombol Cetak -->
                                                <button
                                                    v-if="pengajuan.status === 'selesai'"
                                                    @click="openCetakModal(pengajuan)"
                                                    class="text-green-600 hover:text-green-900"
                                                >
                                                    Cetak
                                                </button>

                                                <span
                                                    v-else
                                                    class="text-gray-400 cursor-not-allowed px-4 py-2 text-sm"
                                                    >Cetak</span
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- No Results -->
                            <div
                                v-if="pengajuanList.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400 mb-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                                <p>Tidak ada pengajuan yang ditemukan</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="pagination && pagination.last_page > 1"
                            class="mt-6 flex items-center justify-between"
                        >
                            <div class="flex items-center">
                                <span class="text-sm text-gray-700">
                                    Halaman {{ pagination.current_page }} dari
                                    {{ pagination.last_page }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="goToPage(pagination.prev_page_url)"
                                    :disabled="!pagination.prev_page_url"
                                    :class="[
                                        'px-3 py-1 rounded-md text-sm',
                                        pagination.prev_page_url
                                            ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                                    ]"
                                >
                                    Sebelumnya
                                </button>

                                <div class="flex space-x-1">
                                    <template v-for="page in pagination.links" :key="page.label">
                                        <button
                                            v-if="
                                                page.url &&
                                                !page.label.includes('Previous') &&
                                                !page.label.includes('Next')
                                            "
                                            @click="goToPage(page.url)"
                                            :class="[
                                                'px-3 py-1 rounded-md text-sm',
                                                page.active
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50',
                                            ]"
                                        >
                                            {{ page.label }}
                                        </button>
                                    </template>
                                </div>

                                <button
                                    @click="goToPage(pagination.next_page_url)"
                                    :disabled="!pagination.next_page_url"
                                    :class="[
                                        'px-3 py-1 rounded-md text-sm',
                                        pagination.next_page_url
                                            ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                                    ]"
                                >
                                    Selanjutnya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail & Proses dengan Fixed Header/Footer -->
        <Modal :show="showDetailModal" @close="closeModal" max-width="4xl">
            <div class="flex flex-col h-[80vh]" v-if="selectedPengajuan">
                <!-- Fixed Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-white">
                    <h2 class="text-lg font-medium text-gray-900">
                        Detail Pengajuan: {{ selectedPengajuan.jenis_surat.nama_surat }}
                    </h2>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="space-y-4 text-sm">
                        <!-- Data Pemohon, Lampiran, dll. -->
                        <p>
                            <strong>Pemohon:</strong>
                            {{ selectedPengajuan.data_pemohon.nama }} (NIK:
                            {{ selectedPengajuan.data_pemohon.nik }})
                        </p>
                        <div>
                            <strong>Lampiran:</strong>
                            <ul class="list-disc list-inside ml-4 mt-2">
                                <li v-for="(path, name) in selectedPengajuan.lampiran" :key="name">
                                    <a
                                        :href="'/storage/' + path"
                                        target="_blank"
                                        class="text-indigo-600 hover:underline"
                                        >{{ name.replace(/_/g, ' ') }}</a
                                    >
                                </li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t">
                            <InputLabel for="nomor_surat" value="Nomor Surat" />
                            <input
                                v-model="form.nomor_surat"
                                id="nomor_surat"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="cth: 005/DS/I/2025"
                            />
                        </div>

                        <div class="pt-4 border-t">
                            <h3 class="font-medium mb-4">Update Status Pengajuan</h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="status" value="Status" />
                                    <select
                                        v-model="form.status"
                                        id="status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel
                                        for="keterangan_admin"
                                        value="Keterangan / Alasan (jika ditolak)"
                                    />
                                    <textarea
                                        v-model="form.keterangan_admin"
                                        id="keterangan_admin"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Tutup</SecondaryButton>
                        <PrimaryButton @click="updateStatus" :disabled="form.processing">
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Modal Cetak dengan Fixed Layout -->
        <Modal :show="showCetakModal" @close="closeCetakModal" max-width="2xl">
            <div class="flex flex-col h-auto" v-if="selectedPengajuanCetak">
                <!-- Fixed Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-white">
                    <div class="text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400 mb-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            ></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">Pilih Format Cetak</h2>
                        <p class="text-gray-600 mt-1">
                            {{ selectedPengajuanCetak.jenis_surat.nama_surat }}
                        </p>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-6">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Opsi PDF -->
                        <a
                            :href="route('admin.proses.cetak', selectedPengajuanCetak.id)"
                            target="_blank"
                            @click="closeCetakModal"
                            class="group p-6 border-2 border-gray-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-all duration-200 cursor-pointer"
                        >
                            <div class="flex flex-col items-center">
                                <svg
                                    class="h-12 w-12 text-red-500 mb-3 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">PDF</h3>
                                <p class="text-sm text-gray-500 text-center">
                                    Format portable, cocok untuk dilihat dan dicetak
                                </p>
                            </div>
                        </a>

                        <!-- Opsi Word -->
                        <a
                            :href="route('admin.proses.cetakWord', selectedPengajuanCetak.id)"
                            target="_blank"
                            @click="closeCetakModal"
                            class="group p-6 border-2 border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 cursor-pointer"
                        >
                            <div class="flex flex-col items-center">
                                <svg
                                    class="h-12 w-12 text-blue-500 mb-3 group-hover:scale-110 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Word</h3>
                                <p class="text-sm text-gray-500 text-center">
                                    Format dokumen, dapat diedit lebih lanjut
                                </p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <SecondaryButton @click="closeCetakModal" class="w-full">
                        Batal
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
    /* Smooth transitions untuk hover effects */
    .group:hover svg {
        transform: scale(1.1);
    }
</style>
