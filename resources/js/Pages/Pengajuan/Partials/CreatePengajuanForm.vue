<script setup>
import { computed } from 'vue';
import { usePengajuanUtils } from '@/Composables/usePengajuanUtils';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    form: Object,
    jenisSuratTersedia: Array,
});

const { slugify } = usePengajuanUtils();

const selectedJenisSurat = computed(() => {
    if (props.form.jenis_surat_id) {
        return props.jenisSuratTersedia.find((s) => s.id === props.form.jenis_surat_id);
    }
    return null;
});

const handleFileChange = (syarat, event) => {
    const file = event.target.files[0];
    const key = slugify(syarat);
    if (file) {
        props.form.lampiran[key] = file;
    } else {
        delete props.form.lampiran[key];
    }
};
</script>

<template>
    <div class="p-6 space-y-6">
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Jenis Surat</h3>
                <p class="text-sm text-gray-600">Pilih jenis surat yang ingin Anda ajukan dari daftar di bawah ini.</p>
            </div>
            
            <div class="space-y-4">
                <div>
                    <InputLabel for="jenis_surat_id" value="Jenis Surat" />
                    <select
                        id="jenis_surat_id"
                        v-model="form.jenis_surat_id"
                        class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm py-3 px-4"
                    >
                        <option value="" disabled>-- Pilih Jenis Surat --</option>
                        <option v-for="surat in jenisSuratTersedia" :key="surat.id" :value="surat.id">
                            {{ surat.nama_surat }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.jenis_surat_id" />
                </div>
            </div>
        </div>

        <div v-if="selectedJenisSurat && selectedJenisSurat.syarat.length" class="bg-white rounded-lg p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Dokumen yang Diperlukan</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="flex-shrink-0 w-5 h-5 text-amber-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Persyaratan Upload</p>
                            <ul class="mt-1 text-sm text-amber-700 list-disc list-inside space-y-1">
                                <li>Format file harus PDF</li>
                                <li>Ukuran maksimal 2MB per file</li>
                                <li>Pastikan dokumen dapat dibaca dengan jelas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-6">
                <div v-for="(syarat, index) in selectedJenisSurat.syarat" :key="syarat" class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">
                            {{ index + 1 }}
                        </span>
                        <InputLabel :for="slugify(syarat)" :value="syarat" class="text-base" />
                    </div>
                    <div class="ml-8">
                        <input
                            :id="slugify(syarat)"
                            type="file"
                            accept=".pdf"
                            @change="(event) => handleFileChange(syarat, event)"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer"
                        />
                        <InputError class="mt-2" :message="form.errors[`lampiran.${slugify(syarat)}`]" />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="selectedJenisSurat" class="bg-white rounded-lg p-6 shadow-sm">
             <div class="flex items-start">
                <svg class="flex-shrink-0 w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-base font-medium text-blue-900">Informasi Proses</h4>
                    <div class="mt-2 text-sm text-blue-800">
                        <p>Setelah pengajuan disubmit, dokumen akan diverifikasi oleh admin. Anda akan mendapat notifikasi melalui email tentang status pengajuan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>