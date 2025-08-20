<script setup>
defineProps({
    jenisSuratList: Array,
});

const emit = defineEmits(['view', 'delete']);
</script>

<template>
    <!-- Tabel Data untuk Desktop -->
    <div class="overflow-x-auto hidden md:block">
        <table class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="w-1/4 py-3 px-4 text-left font-semibold">Nama Surat</th>
                    <th class="w-2/4 py-3 px-4 text-left font-semibold">Deskripsi</th>
                    <th class="w-1/4 py-3 px-4 text-left font-semibold">Template</th>
                    <th class="w-1/4 py-3 px-4 text-left font-semibold">Syarat</th>
                    <th class="w-1/4 py-3 px-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="surat in jenisSuratList" :key="surat.id" class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium">{{ surat.nama_surat }}</td>
                    <td class="py-3 px-4 text-gray-600">
                        <div class="max-w-xs truncate" :title="surat.deskripsi">
                            {{ surat.deskripsi || '-' }}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span v-if="surat.template_surat" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ surat.template_surat }}
                        </span>
                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            Tidak ada
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div v-if="surat.syarat && surat.syarat.length > 0" class="flex flex-wrap gap-1">
                            <span v-for="(syarat, index) in surat.syarat.slice(0, 2)" :key="index" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ syarat }}
                            </span>
                            <span v-if="surat.syarat.length > 2" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                +{{ surat.syarat.length - 2 }} lagi
                            </span>
                        </div>
                        <span v-else class="text-gray-400 text-sm">Tidak ada syarat</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <button @click="$emit('view', surat)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Detail
                            </button>
                            <span class="text-gray-300">|</span>
                            <button @click="$emit('delete', surat)" class="text-red-600 hover:text-red-900 font-medium">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Tampilan Kartu untuk Mobile -->
    <div class="md:hidden space-y-4">
        <div v-for="surat in jenisSuratList" :key="surat.id" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-900">{{ surat.nama_surat }}</h3>
                <div class="flex space-x-2">
                    <button @click="$emit('view', surat)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        Detail
                    </button>
                    <button @click="$emit('delete', surat)" class="text-red-600 hover:text-red-900 text-sm font-medium">
                        Hapus
                    </button>
                </div>
            </div>
            
            <div class="space-y-2 text-sm">
                <div v-if="surat.deskripsi">
                    <span class="text-gray-500">Deskripsi:</span>
                    <p class="text-gray-700 mt-1">{{ surat.deskripsi }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Template:</span>
                    <span v-if="surat.template_surat" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ surat.template_surat }}
                    </span>
                    <span v-else class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                        Tidak ada
                    </span>
                </div>
                <div v-if="surat.syarat && surat.syarat.length > 0">
                    <span class="text-gray-500">Syarat:</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        <span v-for="syarat in surat.syarat" :key="syarat" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                            {{ syarat }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>