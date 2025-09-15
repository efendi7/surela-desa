<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { FilePlus, Clock, CheckCircle, FileText, Download, Loader, XCircle } from 'lucide-vue-next';

// --- PROPS ---
const props = defineProps({
    userStatistics: {
        type: Object,
        default: () => ({
            totalSubmissions: 0,
            pendingSubmissions: 0,
            processedSubmissions: 0,
            completedSubmissions: 0,
            rejectedSubmissions: 0,
        }),
    },
    userSubmissions: {
        type: Array,
        default: () => [],
    },
});

// --- COMPUTED PROPERTIES ---
const user = computed(() => usePage().props.auth.user);

// --- HELPER FUNCTIONS ---
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'diproses': 'bg-blue-100 text-blue-800 border-blue-200',
        'selesai': 'bg-green-100 text-green-800 border-green-200',
        'ditolak': 'bg-red-100 text-red-800 border-red-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getDetailRoute = () => {
    return route('pengajuan.index');
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Dashboard Anda
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Welcome Section -->
                <div class="bg-white p-6 sm:p-8 shadow-xl sm:rounded-2xl flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ user.name }}!</h3>
                        <p class="text-gray-600 mt-1">Siap untuk mengurus surat hari ini?</p>
                    </div>
                    <Link :href="route('pengajuan.index')" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5">
                        <FilePlus class="h-5 w-5" />
                        Ajukan Surat Baru
                    </Link>
                </div>

               <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
    <div class="bg-white p-4 lg:p-5 rounded-xl shadow-md border border-gray-100 text-center lg:text-left lg:flex lg:items-center lg:gap-4">
        <div class="inline-flex p-3 bg-gray-100 rounded-lg mb-2 lg:mb-0">
            <FileText class="h-6 w-6 text-gray-600"/>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
            <p class="text-2xl font-bold text-gray-900">{{ userStatistics.totalSubmissions }}</p>
        </div>
    </div>

    <div class="bg-white p-4 lg:p-5 rounded-xl shadow-md border border-gray-100 text-center lg:text-left lg:flex lg:items-center lg:gap-4">
        <div class="inline-flex p-3 bg-yellow-100 rounded-lg mb-2 lg:mb-0">
            <Clock class="h-6 w-6 text-yellow-600"/>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-gray-900">{{ userStatistics.pendingSubmissions }}</p>
        </div>
    </div>

    <div class="bg-white p-4 lg:p-5 rounded-xl shadow-md border border-gray-100 text-center lg:text-left lg:flex lg:items-center lg:gap-4">
        <div class="inline-flex p-3 bg-blue-100 rounded-lg mb-2 lg:mb-0">
            <Loader class="h-6 w-6 text-blue-600"/>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Diproses</p>
            <p class="text-2xl font-bold text-gray-900">{{ userStatistics.processedSubmissions }}</p>
        </div>
    </div>

    <div class="bg-white p-4 lg:p-5 rounded-xl shadow-md border border-gray-100 text-center lg:text-left lg:flex lg:items-center lg:gap-4">
        <div class="inline-flex p-3 bg-green-100 rounded-lg mb-2 lg:mb-0">
            <CheckCircle class="h-6 w-6 text-green-600"/>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Selesai</p>
            <p class="text-2xl font-bold text-gray-900">{{ userStatistics.completedSubmissions }}</p>
        </div>
    </div>

    <div class="bg-white p-4 lg:p-5 rounded-xl shadow-md border border-gray-100 text-center lg:text-left lg:flex lg:items-center lg:gap-4">
        <div class="inline-flex p-3 bg-red-100 rounded-lg mb-2 lg:mb-0">
            <XCircle class="h-6 w-6 text-red-600"/>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Ditolak</p>
            <p class="text-2xl font-bold text-gray-900">{{ userStatistics.rejectedSubmissions }}</p>
        </div>
    </div>
</div>

                <!-- History Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="p-6 sm:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Riwayat Pengajuan Surat Anda</h3>
                        <div class="overflow-x-auto">
                           <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Surat</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pengajuan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="userSubmissions.length === 0">
                                        <td colspan="4" class="text-center py-10 text-gray-500">
                                            Anda belum memiliki riwayat pengajuan surat.
                                        </td>
                                    </tr>
                                    <tr v-for="submission in userSubmissions" :key="submission.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ submission.jenis_surat.nama_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(submission.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-3 py-1 inline-flex font-semibold rounded-full border" :class="getStatusClass(submission.status)">
                                                {{ submission.status.charAt(0).toUpperCase() + submission.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <Link
                                                v-if="submission.status === 'selesai'"
                                                :href="route('pengajuan.download-final', { pengajuan: submission.id })"
                                                class="inline-flex items-center gap-1 px-3 py-1 font-semibold text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition-all"
                                            >
                                                <Download class="h-4 w-4" />
                                                Unduh
                                            </Link>
                                            <Link
                                                :href="getDetailRoute()"
                                                class="inline-flex items-center px-3 py-1 font-semibold text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 transition-all"
                                            >
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>