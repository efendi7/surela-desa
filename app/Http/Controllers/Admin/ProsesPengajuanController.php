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
            'pengajuanList' => PengajuanSurat::with(['user:id,name', 'jenisSurat:id,nama_surat,slug']) // Tambahkan slug
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
        // 1. Validasi Status: Pastikan surat hanya bisa dicetak jika statusnya 'selesai'.
        // Baris ini diaktifkan kembali untuk keamanan.
        if ($pengajuanSurat->status !== 'selesai') {
            return redirect()->back()->with('error', 'Surat hanya bisa dicetak jika statusnya sudah selesai.');
        }

        // Mengambil data profil desa untuk digunakan di kop surat
        $profilDesa = ProfilDesa::first();
        if (!$profilDesa) {
            return redirect()->back()->with('error', 'Profil desa belum diatur.');
        }

        // 2. Logika Template Dinamis: Memilih template berdasarkan slug jenis surat.
        // Pastikan Anda memiliki kolom 'slug' di tabel 'jenis_surats'.
        // Contoh slug: 'surat-keterangan-domisili', 'surat-keterangan-usaha', dll.
        $viewName = 'surat.' . $pengajuanSurat->jenisSurat->slug;

        // Cek apakah file view template-nya ada
        if (!view()->exists($viewName)) {
            return redirect()->back()->with('error', "Template untuk surat '{$pengajuanSurat->jenisSurat->nama_surat}' tidak ditemukan.");
        }
        
        // Data yang akan dikirim ke view
        $data = [
            'pengajuan' => $pengajuanSurat,
            'profilDesa' => $profilDesa,
            'tanggal' => now()->translatedFormat('d F Y'), // Format tanggal dalam Bahasa Indonesia
        ];

        $pdf = Pdf::loadView($viewName, $data);
        
        // 3. Nama File Dinamis: Membuat nama file PDF sesuai jenis surat dan nama pemohon.
        $namaPemohonSlug = Str::slug($pengajuanSurat->data_pemohon['nama'] ?? 'pemohon');
        $namaFile = $pengajuanSurat->jenisSurat->slug . '-' . $namaPemohonSlug . '.pdf';

        // Menggunakan stream() agar PDF langsung tampil di browser
        return $pdf->stream($namaFile);
    }
}
