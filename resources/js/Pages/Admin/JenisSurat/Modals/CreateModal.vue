<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

// State form
const form = useForm({
    nama_surat: '',
    deskripsi: '',
    syarat: [],
});

const isDropdownOpen = ref(false);

const syaratOptions = [
    'KTP',
    'Kartu Keluarga',
    'Akta Lahir',
    'Surat RT/RW',
    'Foto Usaha',
    'Proposal Kegiatan',
];

// Computed
const selectedSyaratText = computed(() => {
    if (!form.syarat || form.syarat.length === 0) return 'Pilih syarat-syarat...';
    if (form.syarat.length === 1) return form.syarat[0];
    return `${form.syarat.length} syarat dipilih`;
});

// Methods
const closeModal = () => {
    form.reset();
    isDropdownOpen.value = false;
    emit('close');
};

const createSurat = () => {
    form.post(route('admin.jenis-surat.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const toggleSyarat = (syarat) => {
    const index = form.syarat.indexOf(syarat);
    if (index > -1) {
        form.syarat.splice(index, 1);
    } else {
        form.syarat.push(syarat);
    }
};

const isSyaratSelected = (syarat) => form.syarat?.includes(syarat);

// Reset form setiap kali modal dibuka
watch(() => props.show, (newVal) => {
    if (newVal) {
        form.reset({
            nama_surat: '',
            deskripsi: '',
            syarat: [],
        });
    }
});
</script>

<template>
    <!-- Pakai v-if supaya modal di-create ulang setiap kali show = true -->
    <Modal v-if="show" @close="closeModal" max-width="2xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Tambah Jenis Surat Baru</h2>
            <p class="mt-1 text-sm text-gray-600">Isi detail surat yang akan ditambahkan.</p>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4 flex-1 overflow-y-auto">
            <div class="space-y-4">
                <!-- Nama Surat -->
                <div>
                    <InputLabel for="nama_surat" value="Nama Surat" />
                    <TextInput
                        id="nama_surat"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.nama_surat"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.nama_surat" />
                </div>

                <!-- Deskripsi -->
                <div>
                    <InputLabel for="deskripsi" value="Deskripsi" />
                    <TextInput
                        id="deskripsi"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.deskripsi"
                    />
                    <InputError class="mt-2" :message="form.errors.deskripsi" />
                </div>

                <!-- Syarat-syarat -->
                <div>
                    <InputLabel for="syarat" value="Syarat-syarat" />
                    <div class="relative">
                        <button
                            type="button"
                            @click="toggleDropdown"
                            class="relative mt-1 w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-left shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <span class="block truncate">{{ selectedSyaratText }}</span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"
                            >
                                <svg
                                    class="h-5 w-5 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        </button>

                        <div
                            v-if="isDropdownOpen"
                            class="absolute z-20 mt-2 w-full border border-gray-200 rounded-md bg-white shadow-lg"
                        >
                            <div class="p-3 max-h-48 overflow-y-auto">
                                <div class="space-y-2">
                                    <label
                                        v-for="syarat in syaratOptions"
                                        :key="syarat"
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="isSyaratSelected(syarat)"
                                            @change="toggleSyarat(syarat)"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-3 text-sm text-gray-900">{{ syarat }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Tags -->
                    <div
                        v-if="form.syarat && form.syarat.length > 0"
                        class="mt-2 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="syarat in form.syarat"
                            :key="syarat"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                        >
                            {{ syarat }}
                            <button
                                type="button"
                                @click="toggleSyarat(syarat)"
                                class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none"
                            >
                                <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 8 8">
                                    <path
                                        d="M1.41 0l-1.41 1.41.72.72 1.78 1.81-1.78 1.78-.72.69 1.41 1.44.72-.72 1.81-1.81 1.78 1.81.69.72 1.44-1.44-.72-.69-1.81-1.78 1.81-1.81.72-.72-1.44-1.41-.69.72-1.78 1.78-1.81-1.78-.72-.72z"
                                    />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <InputError class="mt-2" :message="form.errors.syarat" />
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div
            class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-2"
        >
            <SecondaryButton @click="closeModal"> Batal </SecondaryButton>
            <PrimaryButton
                @click="createSurat"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Simpan
            </PrimaryButton>
        </div>
    </Modal>
</template>
