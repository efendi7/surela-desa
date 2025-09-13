<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use App\Models\PerangkatDesa;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik dan aktivitas terbaru.
     */
    public function index()
    {
        // Mengambil data untuk kartu statistik
        $statistics = [
            'totalUsers' => User::where('role', 'warga')->count(),
            'pendingSubmissions' => PengajuanSurat::where('status', 'pending')->count(), // Disesuaikan dengan status di Vue
            'processedSubmissions' => PengajuanSurat::whereIn('status', ['selesai', 'ditolak'])->count(), // Disesuaikan
            'letterTypes' => JenisSurat::count(),
            'totalPerangkatDesa' => PerangkatDesa::count(),
        ];

        // Mengambil 10 aktivitas pengajuan surat terbaru
        $recentActivities = PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat'])
            ->latest()
            ->take(10)
            ->get();

        // Data khusus untuk chart
        $chartData = [
            'statusDistribution' => [
                'pending' => PengajuanSurat::where('status', 'pending')->count(),
                'diproses' => PengajuanSurat::where('status', 'diproses')->count(),
                'selesai' => PengajuanSurat::where('status', 'selesai')->count(),
                'ditolak' => PengajuanSurat::where('status', 'ditolak')->count(),
            ],
            'dailyTrend' => $this->getDailySubmissions(), // Menggunakan data harian
            'letterTypeDistribution' => $this->getLetterTypeDistribution()
        ];

        // Me-render komponen Vue dengan data yang sudah diambil
        return Inertia::render('Admin/Dashboard', [
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData
        ]);
    }

    /**
     * Mendapatkan data pengajuan per hari selama 30 hari terakhir untuk line chart.
     */
    private function getDailySubmissions()
    {
        $data = PengajuanSurat::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Memformat data agar siap digunakan oleh ApexCharts
        return [
            'dates' => $data->pluck('date'),
            'totals' => $data->pluck('total'),
        ];
    }

    /**
     * Mendapatkan distribusi jenis surat
     */
    private function getLetterTypeDistribution()
    {
        return PengajuanSurat::join('jenis_surats', 'pengajuan_surats.jenis_surat_id', '=', 'jenis_surats.id')
            ->selectRaw('jenis_surats.nama_surat, COUNT(*) as total')
            ->groupBy('jenis_surats.id', 'jenis_surats.nama_surat')
            ->orderBy('total', 'desc')
            ->limit(5) // Top 5 jenis surat
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->nama_surat => $item->total];
            });
    }

    /**
     * API endpoint khusus untuk data chart
     */
    public function getChartData()
    {
        return response()->json([
            'statusDistribution' => [
                 'pending' => PengajuanSurat::where('status', 'pending')->count(),
                 'diproses' => PengajuanSurat::where('status', 'diproses')->count(),
                 'selesai' => PengajuanSurat::where('status', 'selesai')->count(),
                 'ditolak' => PengajuanSurat::where('status', 'ditolak')->count(),
            ],
            'dailyTrend' => $this->getDailySubmissions(), // Menambahkan data harian ke API
            'letterTypeDistribution' => $this->getLetterTypeDistribution()
        ]);
    }
}
