<script setup>
    import { Link } from '@inertiajs/vue3';

    // Komponen menerima data 'pengajuan' dan prop 'isRiwayat' sebagai saklar mode
    defineProps({
        pengajuan: {
            type: Array,
            required: true,
        },
        isRiwayat: {
            type: Boolean,
            default: false, // Defaultnya adalah mode 'Pengajuan Aktif'
        },
    });

    // Mendefinisikan semua event yang akan dikirim ke parent component
    defineEmits([
        'open-detail',
        'trigger-upload',
        'confirm-selesai',
        'delete-file',
        'delete-riwayat',
    ]);

    // Fungsi bantuan untuk memberikan warna pada label status
    const getStatusClass = (status) => {
        switch (status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 border-yellow-300';
            case 'diproses':
                return 'bg-blue-100 text-blue-800 border-blue-300';
            case 'selesai':
                return 'bg-green-100 text-green-800 border-green-300';
            case 'ditolak':
                return 'bg-red-100 text-red-800 border-red-300';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-300';
        }
    };

    // Fungsi bantuan untuk memformat tanggal
    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };
</script>

<template>
    <div class="border border-gray-200 rounded-lg overflow-x-auto mt-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Pemohon
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Jenis Surat
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Tanggal
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Status
                    </th>
                    <th
                        v-if="!isRiwayat"
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Proses Surat
                    </th>
                    <th
                        v-if="isRiwayat"
                        scope="col"
                        class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider"
                    >
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="pengajuan.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                        <svg
                            class="w-12 h-12 mx-auto text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <p class="mt-4 font-semibold">Tidak ada data pengajuan</p>
                        <p class="mt-1 text-xs">
                            Belum ada data yang sesuai dengan filter yang dipilih.
                        </p>
                    </td>
                </tr>

                <tr
                    v-else
                    v-for="item in pengajuan"
                    :key="item.id"
                    class="hover:bg-gray-50 transition-colors duration-150"
                >
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ item.user.name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ item.user.nik || 'NIK tidak tersedia' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.jenis_surat.nama_surat }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ formatDate(item.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            :class="getStatusClass(item.status)"
                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border"
                        >
                            {{ item.status }}
                        </span>
                    </td>

                    <td
                        v-if="!isRiwayat"
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium space-y-2"
                    >
                        <button
                            @click="$emit('open-detail', item)"
                            class="w-full text-left text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                        >
                            Ubah Status / Detail...
                        </button>
                        
                        <div class="border-t border-gray-200"></div>

                        <div v-if="item.status === 'diproses'" class="space-y-2">
                            <Link
                                :href="route('admin.proses.downloadTemplate', { pengajuan: item.id })"
                                class="flex items-center text-gray-600 hover:text-blue-700 transition-colors duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Unduh Template</span>
                            </Link>
                            
                            <div
                                v-if="!item.file_final"
                                class="flex items-center text-gray-600 hover:text-green-700 transition-colors duration-150 cursor-pointer"
                                @click="$emit('trigger-upload', item)"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Upload Hasil</span>
                            </div>
                            
                            <div v-else class="flex items-center justify-between">
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>File Siap</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="$emit('trigger-upload', item)" class="text-blue-500 hover:text-blue-700 p-1 rounded-full" title="Ubah File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z"></path></svg>
                                    </button>
                                    <button @click="$emit('delete-file', item)" class="text-red-500 hover:text-red-700 p-1 rounded-full" title="Hapus File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <button
                                @click="$emit('confirm-selesai', item)"
                                :disabled="!item.file_final"
                                :class="[
                                    !item.file_final
                                        ? 'text-gray-400 cursor-not-allowed'
                                        : 'text-green-600 hover:text-green-800 font-bold',
                                ]"
                                class="w-full text-left transition-colors duration-150"
                            >
                                Konfirmasi Selesai
                            </button>
                        </div>

                        <div v-else-if="item.status === 'pending'" class="text-xs text-gray-500 italic py-2">
                            Ubah status ke 'diproses' untuk melanjutkan.
                        </div>
                    </td>

                    <td
                        v-if="isRiwayat"
                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                    >
                        <button
                            @click="$emit('open-detail', item)"
                            class="text-indigo-600 hover:text-indigo-900"
                        >
                            Detail
                        </button>
                        <Link
                            v-if="item.status === 'selesai' && item.file_hasil"
                            :href="route('warga.pengajuan.download', { pengajuan: item.id })"
                            class="ml-4 text-green-600 hover:text-green-900"
                        >
                           Unduh
                        </Link>
                        <button
                            @click="$emit('delete-riwayat', item)"
                            class="ml-4 text-red-600 hover:text-red-900 font-semibold"
                        >
                            Hapus
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>