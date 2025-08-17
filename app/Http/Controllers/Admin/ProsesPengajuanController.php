<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\ProfilDesa; // Import model ProfilDesa
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str facade
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ProsesPengajuanController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan yang masuk untuk admin.
     */
    public function index()
    {
        return Inertia::render('Admin/Pengajuan/Index', [
            'pengajuanList' => PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat,slug'])
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Mengubah status pengajuan.
     */
    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_admin' => 'nullable|string|max:1000',
        ]);

        $pengajuanSurat->update([
            'status' => $request->status,
            'keterangan_admin' => $request->keterangan_admin,
        ]);

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Membuat dan menampilkan surat dalam format PDF.
     */
    public function cetakPdf(PengajuanSurat $pengajuanSurat)
    {
        // PERBAIKAN 1: Memuat relasi 'user' dan 'jenisSurat' secara eksplisit.
        // Ini memastikan semua data dari tabel 'users' (seperti NIK, tempat lahir, dll.) tersedia di template.
        $pengajuanSurat->load(['user', 'jenisSurat']);

        // 1. Validasi Status
        if ($pengajuanSurat->status !== 'selesai') {
            return redirect()->back()->with('error', 'Surat hanya bisa dicetak jika statusnya sudah selesai.');
        }

        // Mengambil data profil desa
        $profilDesa = ProfilDesa::first();
        if (!$profilDesa) {
            return redirect()->back()->with('error', 'Profil desa belum diatur.');
        }

        // 2. Logika Template Dinamis
        $viewName = 'surat.' . $pengajuanSurat->jenisSurat->slug;

        if (!view()->exists($viewName)) {
            return redirect()->back()->with('error', "Template untuk surat '{$pengajuanSurat->jenisSurat->nama_surat}' tidak ditemukan.");
        }
        
        // Data yang akan dikirim ke view
        $data = [
            'pengajuan' => $pengajuanSurat,
            'profilDesa' => $profilDesa,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView($viewName, $data);
        
        // PERBAIKAN 2: Menyelaraskan nama pemohon untuk nama file.
        // Menggunakan data dari relasi 'user', sama seperti di template surat.
        $namaPemohonSlug = Str::slug($pengajuanSurat->user->name ?? 'pemohon');
        $namaFile = $pengajuanSurat->jenisSurat->slug . '-' . $namaPemohonSlug . '.pdf';

        return $pdf->stream($namaFile);
    }
}
