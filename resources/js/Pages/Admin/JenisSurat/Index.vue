<script setup>
    import { ref, computed } from 'vue';
    import { Head, router } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
     import DestructiveConfirmationModal from '@/Components/DestructiveConfirmationModal.vue';
    import JenisSuratFilter from './Partials/JenisSuratFilter.vue';
    import JenisSuratTable from './Partials/JenisSuratTable.vue';
    import JenisSuratFormModal from './Partials/JenisSuratFormModal.vue';


    const props = defineProps({
        jenisSurat: Array,
        templateOptions: Array,
    });

    // STATE MANAGEMENT
    const filters = ref({});
    const showFormModal = ref(false);
    const formModalMode = ref('create');
    const selectedSurat = ref(null);
     const showDestructiveConfirmModal = ref(false);
    const destructiveConfirmPayload = ref({});


    const syaratOptions = [
        'KTP',
        'Kartu Keluarga',
        'Akta Lahir',
        'Surat RT/RW',
        'Foto Usaha',
        'Proposal Kegiatan',
    ];

    // COMPUTED PROPERTY UNTUK FILTERING
    const filteredJenisSurat = computed(() => {
        let result = [...props.jenisSurat];
        const { search, template, syarat, sort } = filters.value;

        if (search) {
            const query = search.toLowerCase();
            result = result.filter(
                (s) =>
                    s.nama_surat?.toLowerCase().includes(query) ||
                    s.deskripsi?.toLowerCase().includes(query)
            );
        }
        if (template) {
            if (template === 'ada') result = result.filter((s) => s.template_surat);
            else if (template === 'tidak_ada') result = result.filter((s) => !s.template_surat);
            else result = result.filter((s) => s.template_surat === template);
        }
        if (syarat) {
            result = result.filter((s) => s.syarat && s.syarat.includes(syarat));
        }
        result.sort((a, b) => {
            switch (sort) {
                case 'desc':
                    return (b.nama_surat || '').localeCompare(a.nama_surat || '');
                case 'newest':
                    return new Date(b.created_at) - new Date(a.created_at);
                case 'oldest':
                    return new Date(a.created_at) - new Date(b.created_at);
                default:
                    return (a.nama_surat || '').localeCompare(b.nama_surat || '');
            }
        });
        return result;
    });

    // MODAL & FORM HANDLERS
    const openCreateModal = () => {
        selectedSurat.value = null;
        formModalMode.value = 'create';
        showFormModal.value = true;
    };

    const openViewModal = (surat) => {
        selectedSurat.value = surat;
        formModalMode.value = 'view';
        showFormModal.value = true;
    };

    const handleFormSubmit = (form) => {
        const onSuccess = () => (showFormModal.value = false);
        if (formModalMode.value === 'create') {
            form.post(route('admin.jenis-surat.store'), { onSuccess });
        } else {
            form.put(route('admin.jenis-surat.update', selectedSurat.value.id), { onSuccess });
        }
    };

    // CONFIRMATION MODAL HANDLERS
    const openDeleteModal = (surat) => {
    let message = `Anda akan menghapus jenis surat <strong>"${surat.nama_surat}"</strong>.`;

    // Tambahkan peringatan spesifik jika ada pengajuan aktif
    if (surat.active_pengajuan_count > 0) {
        // Pesan utama yang lebih tegas jika ada pengajuan aktif
        message += ` Saat ini, ada <strong>${surat.active_pengajuan_count} pengajuan aktif</strong> (pending/diproses) yang terkait.`;
        // Tambahkan detail peringatan di dalam box terpisah
        message += `<div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-sm">
                        <strong class="font-semibold text-red-700">PERINGATAN:</strong> Menghapus jenis surat ini akan menyebabkan data pengajuan tersebut menjadi tidak valid. Sebaiknya selesaikan atau tolak pengajuan tersebut terlebih dahulu.
                    </div>`;
    } else {
        // Pesan standar jika tidak ada pengajuan aktif
        message += ` Ini adalah tindakan permanen dan tidak dapat dibatalkan. Pastikan tidak ada lagi pengajuan warga yang memerlukan jenis surat ini.`;
    }

    destructiveConfirmPayload.value = {
        title: 'Konfirmasi Hapus Permanen',
        message: message,
        confirmationWord: 'Hapus',
        confirmText: 'Ya, Saya Yakin & Hapus',
        action: () => router.delete(route('admin.jenis-surat.destroy', surat.id)),
    };
    showDestructiveConfirmModal.value = true;
};

const executeConfirmAction = () => {
    destructiveConfirmPayload.value.action();
    showDestructiveConfirmModal.value = false;
};
</script>

<template>
    <Head title="Manajemen Jenis Surat" />
    <FlashMessage />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jenis Surat</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <PrimaryButton @click="openCreateModal"
                                >Tambah Jenis Surat</PrimaryButton
                            >
                        </div>

                        <JenisSuratFilter
                            :template-options="templateOptions"
                            :syarat-options="syaratOptions"
                            @filter-changed="(newFilters) => (filters = newFilters)"
                            @clear-filters="() => (filters = {})"
                        />

                        <div class="mb-4 text-sm text-gray-600">
                            Menampilkan {{ filteredJenisSurat.length }} dari
                            {{ jenisSurat.length }} jenis surat
                        </div>

                        <JenisSuratTable
                            :jenis-surat-list="filteredJenisSurat"
                            @view="openViewModal"
                            @delete="openDeleteModal"
                        />

                        <div
                            v-if="filteredJenisSurat.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            <p>
                                {{
                                    jenisSurat.length === 0
                                        ? 'Belum ada data jenis surat.'
                                        : 'Tidak ada data yang sesuai dengan filter.'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <JenisSuratFormModal
        :show="showFormModal"
        :mode="formModalMode"
        :initial-data="selectedSurat"
        :template-options="templateOptions"
        :syarat-options="syaratOptions"
        @close="showFormModal = false"
        @submit="handleFormSubmit"
    />

      <DestructiveConfirmationModal
        :show="showDestructiveConfirmModal"
        :title="destructiveConfirmPayload.title"
        :message="destructiveConfirmPayload.message"
        :confirmation-word="destructiveConfirmPayload.confirmationWord"
        :confirm-text="destructiveConfirmPayload.confirmText"
        @close="showDestructiveConfirmModal = false"
        @confirm="executeConfirmAction"
    />
</template>
