<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Controller untuk menangani logika dan data untuk halaman dashboard admin.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // 1. STATISTIK KARTU (JUMLAH PENGAJUAN BERDASARKAN STATUS)
        // Mengambil semua status dalam satu query untuk efisiensi
        $statusCounts = PengajuanSurat::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'pengajuanPending' => $statusCounts->get('pending', 0),
            'pengajuanDiproses' => $statusCounts->get('diproses', 0),
            'pengajuanSelesai' => $statusCounts->get('selesai', 0),
            'pengajuanDitolak' => $statusCounts->get('ditolak', 0),
        ];


        // 2. STATISTIK JENIS SURAT (UNTUK PIE CHART / BAR CHART)
        // Mengambil 5 jenis surat yang paling banyak diajukan
        $statistikJenisSurat = PengajuanSurat::query()
            ->join('jenis_surats', 'pengajuan_surats.jenis_surat_id', '=', 'jenis_surats.id')
            ->select('jenis_surats.nama_surat', DB::raw('count(*) as total'))
            ->groupBy('jenis_surats.nama_surat')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();


        // 3. TREN PENGAJUAN SURAT 30 HARI TERAKHIR (UNTUK LINE CHART)
        $pengajuanPerHari = PengajuanSurat::query()
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('count(*) as jumlah'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal'); // Membuat key dari collection berdasarkan tanggal

        // Menyiapkan data untuk chart, memastikan semua 30 hari ada dalam label
        $tanggalRange = collect(range(0, 29))->map(function ($i) {
            return now()->subDays($i)->format('Y-m-d');
        })->reverse()->values();

        $trenLabels = $tanggalRange->map(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d M'); // Format: 22 Agu
        });

        $trenData = $tanggalRange->map(function ($tanggal) use ($pengajuanPerHari) {
            return $pengajuanPerHari->get($tanggal)->jumlah ?? 0;
        });


        // 4. DAFTAR 5 PENGAJUAN TERBARU (UNTUK TABEL)
        // Menggunakan eager loading (with) untuk menghindari N+1 query problem
        $pengajuanTerbaru = PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat'])
            ->latest() // Mengurutkan dari yang paling baru
            ->limit(5)
            ->get();

            
        // 5. MENGIRIM SEMUA DATA KE INERTIA VIEW
        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'statistikJenisSurat' => $statistikJenisSurat,
            'trenPengajuan' => [
                'labels' => $trenLabels,
                'data' => $trenData,
            ],
            'pengajuanTerbaru' => $pengajuanTerbaru,
        ]);
    }
}