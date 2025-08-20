<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    show: Boolean,
    mode: {
        type: String,
        validator: (value) => ['create', 'view', 'edit'].includes(value),
        required: true,
    },
    initialData: Object,
    templateOptions: Array,
    syaratOptions: Array,
});

const emit = defineEmits(['close', 'submit']);

const currentMode = ref(props.mode);

const form = useForm({
    id: null,
    nama_surat: '',
    deskripsi: '',
    template_surat: '',
    syarat: [],
});

// Mengisi form saat modal dibuka dalam mode 'view' atau 'edit'
watch(() => props.show, (newValue) => {
    currentMode.value = props.mode;
    if (newValue && props.initialData) {
        form.defaults({ ...props.initialData }).reset();
    } else {
        form.defaults({
            id: null,
            nama_surat: '',
            deskripsi: '',
            template_surat: '',
            syarat: [],
        }).reset();
    }
});

const modalTitle = computed(() => {
    if (currentMode.value === 'create') return 'Tambah Jenis Surat Baru';
    if (currentMode.value === 'view') return 'Detail Jenis Surat';
    return 'Edit Jenis Surat';
});

const isFormDisabled = computed(() => currentMode.value === 'view');

// Computed classes untuk styling read-only
const inputClasses = computed(() => {
    const baseClasses = 'mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
    if (isFormDisabled.value) {
        return `${baseClasses} bg-gray-100 bg-opacity-60 text-gray-600 cursor-not-allowed`;
    }
    return baseClasses;
});

const selectClasses = computed(() => {
    const baseClasses = 'mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
    if (isFormDisabled.value) {
        return `${baseClasses} bg-gray-100 bg-opacity-60 text-gray-600 cursor-not-allowed`;
    }
    return baseClasses;
});

const checkboxLabelClasses = computed(() => {
    return (syarat) => {
        const baseClasses = 'flex items-center p-3 border rounded-lg';
        const selectedClasses = isSyaratSelected(syarat) ? 'bg-indigo-50 border-indigo-300' : '';
        
        if (isFormDisabled.value) {
            return `${baseClasses} ${selectedClasses} bg-gray-50 bg-opacity-60 border-gray-200 cursor-not-allowed`;
        }
        return `${baseClasses} ${selectedClasses} cursor-pointer hover:bg-gray-50`;
    };
});

const checkboxTextClasses = computed(() => {
    if (isFormDisabled.value) {
        return 'ml-3 text-sm text-gray-500 select-none';
    }
    return 'ml-3 text-sm text-gray-900 select-none';
});

const isSyaratSelected = (syarat) => form.syarat?.includes(syarat);

const toggleSyarat = (syarat) => {
    if (isFormDisabled.value) return;
    const index = form.syarat.indexOf(syarat);
    if (index > -1) form.syarat.splice(index, 1);
    else form.syarat.push(syarat);
};

const submit = () => {
    emit('submit', form);
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="2xl">
        <div class="flex flex-col max-h-[90vh]">
            <div class="p-6 border-b">
                <h2 class="text-xl font-medium text-gray-900">{{ modalTitle }}</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Nama Surat -->
                <div>
                    <InputLabel for="nama_surat" value="Nama Surat" :class="{'text-gray-500': isFormDisabled}" />
                    <TextInput 
                        id="nama_surat" 
                        type="text" 
                        :class="inputClasses" 
                        v-model="form.nama_surat" 
                        required 
                        :disabled="isFormDisabled" 
                    />
                    <InputError class="mt-2" :message="form.errors.nama_surat" />
                </div>
                
                <!-- Deskripsi -->
                <div>
                    <InputLabel for="deskripsi" value="Deskripsi" :class="{'text-gray-500': isFormDisabled}" />
                    <TextInput 
                        id="deskripsi" 
                        type="text" 
                        :class="inputClasses" 
                        v-model="form.deskripsi" 
                        :disabled="isFormDisabled" 
                    />
                    <InputError class="mt-2" :message="form.errors.deskripsi" />
                </div>
                
                <!-- Template Surat -->
                <div>
                    <InputLabel for="template_surat" value="Template Surat" :class="{'text-gray-500': isFormDisabled}" />
                    <select 
                        id="template_surat" 
                        v-model="form.template_surat" 
                        :disabled="isFormDisabled" 
                        :class="selectClasses"
                    >
                        <option value="">-- Tidak ada template --</option>
                        <option v-for="template in templateOptions" :key="template" :value="template">{{ template }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.template_surat" />
                </div>
                
                <!-- Syarat-syarat -->
                <div>
                    <InputLabel value="Syarat-syarat" :class="{'text-gray-500': isFormDisabled}" />
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <label 
                            v-for="syarat in syaratOptions" 
                            :key="syarat" 
                            :class="checkboxLabelClasses(syarat)"
                        >
                            <input 
                                type="checkbox" 
                                :checked="isSyaratSelected(syarat)" 
                                @change="toggleSyarat(syarat)" 
                                :disabled="isFormDisabled" 
                                class="h-4 w-4 text-indigo-600 rounded"
                                :class="{'opacity-50': isFormDisabled}"
                            />
                            <span :class="checkboxTextClasses">{{ syarat }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.syarat" />
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end p-6 bg-gray-50 border-t space-x-3">
                <template v-if="currentMode === 'create' || currentMode === 'edit'">
                    <SecondaryButton @click="currentMode === 'edit' ? currentMode = 'view' : $emit('close')">Batal</SecondaryButton>
                    <PrimaryButton @click="submit" :disabled="form.processing">Simpan</PrimaryButton>
                </template>
                <template v-else-if="currentMode === 'view'">
                    <SecondaryButton @click="$emit('close')">Tutup</SecondaryButton>
                    <PrimaryButton @click="currentMode = 'edit'">Edit</PrimaryButton>
                </template>
            </div>
        </div>
    </Modal>
</template>