<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { getFotoUrl, getFotoPendukungUrls } from '../utils';

const props = defineProps({
  show: Boolean,
  form: Object,
  isEdit: { type: Boolean, default: false },
  umkm: Object,
  title: String,
  submitText: String,
});

const emit = defineEmits(['submit', 'close']);

const previewFotoProduk = ref(null);
const previewFotoPendukung = ref([]);

const kategoriUsaha = [
  'Makanan & Minuman',
  'Fashion & Aksesori',
  'Kerajinan Tangan',
  'Teknologi & Digital',
  'Jasa & Layanan',
  'Pertanian & Perkebunan',
  'Peternakan & Perikanan',
  'Lainnya',
];

if (props.isEdit && props.umkm) {
  previewFotoProduk.value = props.umkm.foto_produk ? getFotoUrl(props.umkm.foto_produk) : null;
  previewFotoPendukung.value = getFotoPendukungUrls(props.umkm.foto_pendukung);
}

const handleFileSelect = (event, type) => {
  if (type === 'foto_produk') {
    const file = event.target.files[0];
    if (file) {
      props.form.foto_produk = file;
      previewFotoProduk.value = URL.createObjectURL(file);
    }
  } else if (type === 'foto_pendukung') {
    const files = Array.from(event.target.files).slice(0, 3);
    if (files.length > 3) {
      alert('Maksimal 3 foto pendukung diperbolehkan.');
      event.target.value = '';
      return;
    }
    props.form.foto_pendukung = files;
    previewFotoPendukung.value = files.map(file => URL.createObjectURL(file));
  }
};
</script>

<template>
  <Modal :show="show" @close="emit('close')" max-width="4xl">
    <form @submit.prevent="emit('submit')" class="flex flex-col max-h-[90vh] bg-gray-50 overflow-hidden">
      <header class="px-6 py-4 border-b bg-white">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-medium text-gray-900">{{ title }}</h2>
          <button type="button" @click="emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-6">
        <div v-if="!isEdit" class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-md text-sm text-indigo-800">
          <p>
            Mendaftarkan UMKM atas nama: <strong>{{ $page.props.auth.user.name }}</strong>
            (NIK: {{ $page.props.auth.user.nik }})
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <InputLabel :for="`form_nama_usaha_${isEdit}`" value="Nama Usaha *" />
              <TextInput
                :id="`form_nama_usaha_${isEdit}`"
                v-model="form.nama_usaha"
                type="text"
                class="mt-1 block w-full"
                placeholder="Contoh: Warung Bu Siti"
                required
              />
              <InputError class="mt-2" :message="form.errors.nama_usaha" />
            </div>
            <div>
              <InputLabel :for="`form_kategori_usaha_${isEdit}`" value="Kategori Usaha *" />
              <select
                :id="`form_kategori_usaha_${isEdit}`"
                v-model="form.kategori_usaha"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option value="">Pilih Kategori</option>
                <option v-for="kategori in kategoriUsaha" :key="kategori" :value="kategori">
                  {{ kategori }}
                </option>
              </select>
              <InputError class="mt-2" :message="form.errors.kategori_usaha" />
            </div>
            <div v-if="isEdit">
              <InputLabel :for="`form_nama_pemilik_${isEdit}`" value="Nama Pemilik *" />
              <TextInput
                :id="`form_nama_pemilik_${isEdit}`"
                v-model="form.nama_pemilik"
                type="text"
                class="mt-1 block w-full"
                placeholder="Nama lengkap pemilik"
                required
              />
              <InputError class="mt-2" :message="form.errors.nama_pemilik" />
            </div>
            <div v-if="isEdit">
              <InputLabel :for="`form_nik_pemilik_${isEdit}`" value="NIK Pemilik *" />
              <TextInput
                :id="`form_nik_pemilik_${isEdit}`"
                v-model="form.nik_pemilik"
                type="text"
                class="mt-1 block w-full"
                placeholder="Contoh: 1234567890123456"
                required
              />
              <InputError class="mt-2" :message="form.errors.nik_pemilik" />
            </div>
          </div>
          <div class="space-y-4">
            <div>
              <InputLabel :for="`form_nomor_telepon_${isEdit}`" value="Nomor Telepon *" />
              <TextInput
                :id="`form_nomor_telepon_${isEdit}`"
                v-model="form.nomor_telepon"
                type="tel"
                class="mt-1 block w-full"
                placeholder="Contoh: 08123456789"
                required
              />
              <InputError class="mt-2" :message="form.errors.nomor_telepon" />
            </div>
            <div>
              <InputLabel :for="`form_alamat_usaha_${isEdit}`" value="Alamat Usaha *" />
              <TextArea
                :id="`form_alamat_usaha_${isEdit}`"
                v-model="form.alamat_usaha"
                class="mt-1 block w-full"
                rows="3"
                placeholder="Alamat lengkap lokasi usaha"
                required
              />
              <InputError class="mt-2" :message="form.errors.alamat_usaha" />
            </div>
            <div>
              <InputLabel :for="`form_foto_produk_${isEdit}`" :value="isEdit ? 'Foto Sampul' : 'Foto Sampul *'" />
              <input
                :id="`form_foto_produk_${isEdit}`"
                type="file"
                accept="image/jpeg,image/png,image/jpg"
                @change="handleFileSelect($event, 'foto_produk')"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
              />
              <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
              <div v-if="previewFotoProduk" class="mt-2">
                <img :src="previewFotoProduk" class="w-32 h-32 object-cover rounded-md border" />
              </div>
              <InputError class="mt-2" :message="form.errors.foto_produk" />
            </div>
            <div>
              <InputLabel :for="`form_foto_pendukung_${isEdit}`" value="Foto Pendukung (Maks. 3)" />
              <input
                :id="`form_foto_pendukung_${isEdit}`"
                type="file"
                multiple
                accept="image/jpeg,image/png,image/jpg"
                @change="handleFileSelect($event, 'foto_pendukung')"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
              />
              <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 3 foto, masing-masing 2MB</p>
              <div v-if="previewFotoPendukung.length" class="mt-2 flex space-x-2">
                <img
                  v-for="foto in previewFotoPendukung"
                  :key="foto"
                  :src="foto"
                  class="w-16 h-16 object-cover rounded-md border"
                />
              </div>
              <InputError class="mt-2" :message="form.errors.foto_pendukung" />
            </div>
          </div>
        </div>
        <div class="mt-4">
          <InputLabel :for="`form_deskripsi_${isEdit}`" value="Deskripsi Usaha *" />
          <TextArea
            :id="`form_deskripsi_${isEdit}`"
            v-model="form.deskripsi"
            class="mt-1 block w-full"
            rows="4"
            placeholder="Jelaskan tentang usaha Anda, produk/jasa yang ditawarkan, dan keunggulannya"
            required
          />
          <InputError class="mt-2" :message="form.errors.deskripsi" />
        </div>
      </div>

      <footer class="flex justify-end items-center px-6 py-4 border-t bg-white space-x-3">
        <SecondaryButton @click="emit('close')" :disabled="form.processing">
          Batal
        </SecondaryButton>
        <PrimaryButton type="submit" :disabled="form.processing">
          <span v-if="form.processing">Menyimpan...</span>
          <span v-else>{{ submitText }}</span>
        </PrimaryButton>
      </footer>
    </form>
  </Modal>
</template>
