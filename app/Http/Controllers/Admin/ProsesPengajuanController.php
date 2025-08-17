<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ProsesPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat,slug']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('nomor_surat', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis surat
        if ($request->filled('jenis_surat')) {
            $query->whereHas('jenisSurat', function ($jenisQuery) use ($request) {
                $jenisQuery->where('nama_surat', $request->jenis_surat);
            });
        }

        // Sorting
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy('created_at', $sortOrder);
        // Pagination
        $perPage = $request->get('per_page', 10);
        $pengajuanList = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Pengajuan/Index', [
            'pengajuanList' => $pengajuanList->items(),
            'pagination' => [
                'current_page' => $pengajuanList->currentPage(),
                'last_page' => $pengajuanList->lastPage(),
                'per_page' => $pengajuanList->perPage(),
                'total' => $pengajuanList->total(),
                'from' => $pengajuanList->firstItem(),
                'to' => $pengajuanList->lastItem(),
                'prev_page_url' => $pengajuanList->previousPageUrl(),
                'next_page_url' => $pengajuanList->nextPageUrl(),
                'links' => $pengajuanList->linkCollection()->toArray(),
            ],
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'jenis_surat' => $request->jenis_surat,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_admin' => 'nullable|string|max:1000',
            'nomor_surat' => 'nullable|string|max:255',
        ]);

        $pengajuanSurat->update([
            'status' => $request->status,
            'keterangan_admin' => $request->keterangan_admin,
            'nomor_surat' => $request->nomor_surat,
        ]);

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * CETAK PDF
     */
    public function cetakPdf(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->load(['user', 'jenisSurat']);

        if ($pengajuanSurat->status !== 'selesai') {
            return redirect()->back()->with('error', 'Surat hanya bisa dicetak jika status sudah selesai.');
        }

        $profilDesa = ProfilDesa::firstOrFail();

        $data = [
            'pengajuan' => $pengajuanSurat,
            'profilDesa' => $profilDesa,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        // Semua pakai general.blade.php
        $pdf = Pdf::loadView('surat.general', $data);

        $namaPemohonSlug = Str::slug($pengajuanSurat->user->name ?? 'pemohon');
        $namaFile = $pengajuanSurat->jenisSurat->slug . '-' . $namaPemohonSlug . '.pdf';

        return $pdf->stream($namaFile);
    }

    /**
     * CETAK WORD
     */
    public function cetakWord(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->load(['user', 'jenisSurat']);

        if ($pengajuanSurat->status !== 'selesai') {
            return redirect()->back()->with('error', 'Surat hanya bisa dicetak jika status sudah selesai.');
        }

        $profilDesa = ProfilDesa::firstOrFail();

        $data = [
            'pengajuan' => $pengajuanSurat,
            'profilDesa' => $profilDesa,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        $content = view('surat.general', $data)->render();

        $namaPemohonSlug = Str::slug($pengajuanSurat->user->name ?? 'pemohon');
        $namaFile = $pengajuanSurat->jenisSurat->slug . '-' . $namaPemohonSlug . '.doc';

        return Response::make($content, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="'.$namaFile.'"',
        ]);
    }

    public function riwayat()
    {
        $riwayatPengajuan = PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat,slug'])
            ->whereIn('status', ['selesai', 'ditolak'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Pengajuan/Riwayat', [
            'riwayatPengajuan' => $riwayatPengajuan,
        ]);
    }

    /**
     * Show detail pengajuan
     */
    public function show(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->load(['user', 'jenisSurat']);

        return Inertia::render('Admin/Pengajuan/Detail', [
            'pengajuan' => $pengajuanSurat,
        ]);
    }

    /**
     * Bulk update status (untuk update multiple pengajuan sekaligus)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'pengajuan_ids' => 'required|array',
            'pengajuan_ids.*' => 'exists:pengajuan_surats,id',
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_admin' => 'nullable|string|max:1000',
        ]);

        $updated = PengajuanSurat::whereIn('id', $request->pengajuan_ids)
            ->update([
                'status' => $request->status,
                'keterangan_admin' => $request->keterangan_admin,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', "Berhasil mengupdate {$updated} pengajuan.");
    }

    /**
     * Export data pengajuan ke Excel/CSV
     */
    public function export(Request $request)
    {
        $query = PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat,slug']);

        // Apply same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('nomor_surat', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->whereHas('jenisSurat', function ($jenisQuery) use ($request) {
                $jenisQuery->where('nama_surat', $request->jenis_surat);
            });
        }

        $pengajuanList = $query->latest()->get();

        // Create CSV content
        $csvContent = "No,Pemohon,NIK,Jenis Surat,Nomor Surat,Status,Tanggal Pengajuan,Keterangan Admin\n";
        
        foreach ($pengajuanList as $index => $pengajuan) {
            $csvContent .= sprintf(
                '%d,"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $index + 1,
                $pengajuan->user->name ?? '',
                $pengajuan->data_pemohon['nik'] ?? '',
                $pengajuan->jenisSurat->nama_surat ?? '',
                $pengajuan->nomor_surat ?? '',
                ucfirst($pengajuan->status),
                $pengajuan->created_at->format('d/m/Y H:i'),
                str_replace('"', '""', $pengajuan->keterangan_admin ?? '')
            );
        }

        $filename = 'pengajuan_surat_' . date('Y-m-d_H-i-s') . '.csv';

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $stats = [
            'total' => PengajuanSurat::count(),
            'pending' => PengajuanSurat::where('status', 'pending')->count(),
            'diproses' => PengajuanSurat::where('status', 'diproses')->count(),
            'selesai' => PengajuanSurat::where('status', 'selesai')->count(),
            'ditolak' => PengajuanSurat::where('status', 'ditolak')->count(),
            'bulan_ini' => PengajuanSurat::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'minggu_ini' => PengajuanSurat::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        return response()->json($stats);
    }
}