<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Users, FileText, CheckSquare, Mail, Briefcase, Activity, LineChart, PieChart } from 'lucide-vue-next';

// Props
const props = defineProps({
    statistics: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            pendingSubmissions: 0,
            processedSubmissions: 0,
            letterTypes: 0,
            totalPerangkatDesa: 0,
        }),
    },
    recentActivities: {
        type: Array,
        default: () => [],
    },
    chartData: {
        type: Object,
        default: () => ({
            statusDistribution: {},
            dailyTrend: { dates: [], totals: [] }
        })
    }
});

// Computed user
const user = computed(() => usePage().props.auth.user);

// Refs for charts
const statusChartRef = ref(null);
const dailyTrendChartRef = ref(null);

// Format date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Status class for UI
const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 border border-amber-200 shadow-sm',
        'diproses': 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border border-blue-200 shadow-sm',
        'selesai': 'bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 border border-emerald-200 shadow-sm',
        'ditolak': 'bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border border-red-200 shadow-sm',
    };
    return classes[status] || 'bg-gradient-to-r from-gray-50 to-slate-50 text-gray-700 border border-gray-200 shadow-sm';
};

// Determine detail route based on status
const getDetailRoute = (status) => {
    if (status === 'selesai' || status === 'ditolak') {
        return 'admin.proses.riwayat';
    }
    return 'admin.proses.index';
};

// Chart initialization
onMounted(() => {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@latest';
    script.onload = () => {
        initCharts();
    };
    document.head.appendChild(script);
});

const initCharts = () => {
    const dailyTrendData = props.chartData.dailyTrend.totals || [];
    const maxDailyValue = dailyTrendData.length > 0 ? Math.max(...dailyTrendData) : 0;
    
    // 1. Status Distribution Chart (Donut)
    const statusChartOptions = {
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: "'Poppins', sans-serif",
        },
        series: Object.values(props.chartData.statusDistribution),
        labels: ['Pending', 'Diproses', 'Selesai', 'Ditolak'],
        colors: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { show: true },
                        value: { show: true, fontSize: '24px', fontWeight: 700 },
                        total: { show: true, label: 'Total Pengajuan' }
                    }
                }
            }
        },
        legend: { position: 'bottom' },
        title: {
            text: 'Distribusi Status Pengajuan',
            align: 'center',
            style: { 
                fontSize: '16px', 
                fontWeight: 400,
                color: '#6B7280',
                fontFamily: "'Poppins', sans-serif",
            }
        },
    };

    // 2. Daily Submissions Trend Chart (Line)
    const dailyTrendChartOptions = {
        chart: {
            type: 'line',
            height: 350,
            fontFamily: "'Poppins', sans-serif",
            zoom: {
                enabled: false
            },
            toolbar: {
                show: true
            }
        },
        series: [{
            name: 'Jumlah Pengajuan',
            data: props.chartData.dailyTrend.totals
        }],
        xaxis: {
            categories: props.chartData.dailyTrend.dates,
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 400,
                    fontFamily: "'Poppins', sans-serif",
                }
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Pengajuan',
                style: {
                    fontFamily: "'Poppins', sans-serif",
                    fontWeight: 400,
                    color: '#6B7280'
                }
            },
            min: 0,
            forceNiceScale: true,
            labels: {
                formatter: function (val) {
                    return val.toFixed(0);
                },
                style: {
                    fontFamily: "'Poppins', sans-serif",
                    fontWeight: 400,
                }
            },
            tickAmount: (maxDailyValue > 0 && maxDailyValue < 6) ? maxDailyValue + 1 : undefined,
        },
        colors: ['#4F46E5'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        title: {
            text: 'Tren Pengajuan Surat (30 Hari Terakhir)',
            align: 'center',
            style: {
                fontSize: '16px',
                fontWeight: 400,
                color: '#6B7280',
                fontFamily: "'Poppins', sans-serif",
            }
        },
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 4,
            row: {
                colors: ['#f3f4f6', 'transparent'],
                opacity: 0.5
            },
        },
        tooltip: {
            x: {
                format: 'dd MMMM yyyy'
            },
        }
    };

    if (statusChartRef.value) {
        const statusChart = new ApexCharts(statusChartRef.value, statusChartOptions);
        statusChart.render();
    }

    if (dailyTrendChartRef.value) {
        const dailyTrendChart = new ApexCharts(dailyTrendChartRef.value, dailyTrendChartOptions);
        dailyTrendChart.render();
    }
};
</script>

<template>
    <Head title="Admin Dashboard">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Dashboard Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Welcome Banner -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl relative">
                    <div class="p-6 sm:p-8">
                        <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ user.name }}!</h3>
                        <p class="text-gray-600 mt-1">Berikut adalah ringkasan aktivitas dan statistik sistem.</p>
                    </div>
                    <div class="absolute bottom-0 right-0 text-white opacity-10 pointer-events-none overflow-hidden sm:rounded-br-2xl">
                        <svg width="250" height="120" viewBox="0 0 250 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M 20 120 V 60 L 45 40 L 70 60 V 120 Z" stroke-width="2" stroke="#4b5563"/>
                            <rect x="35" y="70" width="20" height="20" stroke-width="2" stroke="#4b5563"/>
                            <path d="M 80 120 V 50 L 110 20 L 140 50 V 120 Z" stroke-width="2" stroke="#4b5563"/>
                            <rect x="95" y="80" width="30" height="40" stroke-width="2" stroke="#4b5563" />
                            <rect x="100" y="60" width="20" height="15" stroke-width="2" stroke="#4b5563" />
                            <path d="M 150 120 V 70 L 165 60 L 180 70 V 120 Z" stroke-width="2" stroke="#4b5563"/>
                            <path d="M 190 120 V 30 L 200 20 L 210 30 V 120" stroke-width="2" stroke="#4b5563"/>
                            <line x1="190" y1="50" x2="210" y2="50" stroke-width="2" stroke="#4b5563"/>
                        </svg>
                    </div>
                </div>


                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                    <!-- Total Pengguna -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6 flex items-center gap-4 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                            <Users class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.totalUsers }}</p>
                        </div>
                    </div>
                    <!-- Pengajuan Pending -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6 flex items-center gap-4 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-amber-500 to-yellow-500 text-white shadow-lg">
                            <FileText class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pengajuan Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.pendingSubmissions }}</p>
                        </div>
                    </div>
                    <!-- Surat Diproses -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6 flex items-center gap-4 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg">
                            <CheckSquare class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Surat Diproses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.processedSubmissions }}</p>
                        </div>
                    </div>
                    <!-- Jenis Surat -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6 flex items-center gap-4 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-lg">
                            <Mail class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jenis Surat</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.letterTypes }}</p>
                        </div>
                    </div>
                    <!-- Perangkat Desa -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6 flex items-center gap-4 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white shadow-lg">
                            <Briefcase class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Perangkat Desa</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.totalPerangkatDesa }}</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Status Chart -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <PieChart class="h-6 w-6 text-indigo-600" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Status Pengajuan</h3>
                        </div>
                        <div ref="statusChartRef"></div>
                    </div>

                    <!-- Daily Trend Chart -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <LineChart class="h-6 w-6 text-blue-600" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Statistik Pengajuan Harian</h3>
                        </div>
                        <div ref="dailyTrendChartRef"></div>
                    </div>
                </div>

                <!-- Recent Activities Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-slate-100 p-2 rounded-lg">
                                <Activity class="h-6 w-6 text-slate-600" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Aktivitas Terbaru</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemohon</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Surat</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-4"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr v-if="recentActivities.length === 0" class="hover:bg-gray-50 transition-colors">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <Activity class="h-8 w-8 text-gray-300" />
                                                <span>Tidak ada aktivitas terbaru.</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-for="activity in recentActivities" :key="activity.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ activity.user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ activity.jenis_surat.nama_surat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(activity.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusClass(activity.status)">
                                                {{ activity.status.charAt(0).toUpperCase() + activity.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route(getDetailRoute(activity.status), { id: activity.id })" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-lg border border-indigo-200 hover:border-indigo-600 transition-all duration-200">
                                                Lihat Detail
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

<style scoped>
.apexcharts-canvas {
    font-family: 'Poppins', sans-serif !important;
}
</style>

