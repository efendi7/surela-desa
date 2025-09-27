<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    berita: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    _method: props.berita ? 'patch' : 'post',
    type: props.berita?.type ?? 'berita',
    title: props.berita?.title ?? '',
    content: props.berita?.content ?? '',
    image: null,
    file: null, 
    published_at: props.berita?.published_at?.slice(0, 16) ?? new Date().toISOString().slice(0, 16),
});

const submit = () => {
    const url = props.berita
        ? route('admin.berita.update', props.berita.id)
        : route('admin.berita.store');

    form.post(url, {
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="mt-6 space-y-6">
       <div>
            <InputLabel for="type" value="Jenis Postingan" />
            <select id="type" v-model="form.type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="berita">Berita</option>
                <option value="pengumuman">Pengumuman</option>
            </select>
            <InputError class="mt-2" :message="form.errors.type" />
        </div>
        <div>
            <InputLabel for="title" value="Judul Berita" />
            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="content" value="Isi Konten" />
            <textarea id="content" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.content" rows="6" required></textarea>
            <InputError class="mt-2" :message="form.errors.content" />
        </div>
        
        <div>
            <InputLabel for="image" value="Gambar Banner (Opsional)" />
            <input type="file" @input="form.image = $event.target.files[0]" class="mt-1 block w-full" />
            <InputError class="mt-2" :message="form.errors.image" />
        </div>

          <div v-if="form.type === 'pengumuman'">
            <InputLabel for="file" value="Upload Surat PDF (Wajib untuk Pengumuman)" />
            <input type="file" @input="form.file = $event.target.files[0]" class="mt-1 block w-full" accept=".pdf" />
            <InputError class="mt-2" :message="form.errors.file" />
            <p v-if="berita?.file_path" class="text-sm text-gray-500 mt-2">
                File saat ini: <a :href="`/storage/${berita.file_path}`" target="_blank" class="text-indigo-600">Lihat File</a>
            </p>
        </div>
        
        <div>
            <InputLabel for="published_at" value="Tanggal Publikasi" />
            <TextInput id="published_at" type="datetime-local" class="mt-1 block w-full" v-model="form.published_at" required />
            <InputError class="mt-2" :message="form.errors.published_at" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
            <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Tersimpan.</p>
            </Transition>
        </div>
    </form>
</template>