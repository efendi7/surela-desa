<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Eye, Target, BookOpen } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProfileHeader from '@/Pages/Admin/ProfilDesa/Partials/ProfileHeader.vue';
import VisiMisiSection from './Partials/VisiMisiSection.vue';
import StrukturOrganisasiSection from './Partials/StrukturOrganisasiSection.vue';
import HtmlContentSection from './Partials/SejarahDesaSection.vue';

// --- PROPS ---
const props = defineProps({
    profilDesa: {
        type: Object,
        required: true,
    },
    pageTitle: {
        type: String,
        default: 'Profil Desa',
    },
    content: {
        type: [String, Array],
        default: '',
    },
    contentType: {
        type: String,
        required: true,
        validator: (value) => ['visi-misi', 'struktur', 'html'].includes(value),
    },
});

// --- COMPOSABLES ---
const useLocationString = (profilDesa) => {
    return computed(() => {
        if (!profilDesa) return 'Lokasi belum diatur';
        
        const namaKecamatan = profilDesa.nama_kecamatan || profilDesa.kecamatan;
        const namaKabupaten = profilDesa.nama_kabupaten || profilDesa.kabupaten;
        const namaProvinsi = profilDesa.nama_provinsi || profilDesa.provinsi;

        return [
            namaKecamatan ? `Kec. ${namaKecamatan}` : '',
            namaKabupaten ? `Kab. ${namaKabupaten}` : '',
            namaProvinsi || '',
        ]
        .filter(Boolean)
        .join(' • ');
    });
};

const useParsedMisi = (misi) => {
    return computed(() => {
        try {
            if (Array.isArray(misi)) {
                return misi;
            }
            
            const misiData = JSON.parse(misi || '[]');
            return Array.isArray(misiData) ? misiData : [];
        } catch (e) {
            return [];
        }
    });
};

// --- COMPUTED PROPERTIES ---
const locationString = useLocationString(props.profilDesa);
const parsedMisi = useParsedMisi(props.profilDesa.misi);

// Content sections mapping
const contentSections = computed(() => ({
    'visi-misi': {
        component: VisiMisiSection,
        props: {
            visi: props.profilDesa.visi,
            misi: parsedMisi.value,
        },
    },
    'struktur': {
        component: StrukturOrganisasiSection,
        props: {
            perangkatDesa: props.content,
        },
    },
    'html': {
        component: HtmlContentSection,
        props: {
            content: props.content,
            pageTitle: props.pageTitle,
        },
    },
}));

const currentSection = computed(() => contentSections.value[props.contentType]);
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ pageTitle }}
                </h2>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Profile Header -->
            <ProfileHeader 
                :profilDesa="profilDesa" 
                :locationString="locationString" 
            />

            <!-- Dynamic Content Section -->
            <component 
                :is="currentSection.component" 
                v-bind="currentSection.props"
                v-if="currentSection"
            />
            
            <!-- Fallback for unknown content types -->
            <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
                <div class="text-center text-gray-500">
                    <p>Tipe konten tidak dikenali: {{ contentType }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>