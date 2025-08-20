<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PengajuanSuratController extends Controller
{
    /**
     * Menampilkan halaman utama pengajuan surat untuk warga.
     * Warga bisa melihat riwayat pengajuan mereka dan membuat pengajuan baru.
     */
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Pengajuan/Index', [
            // Mengambil daftar jenis surat yang bisa diajukan
            'jenisSuratTersedia' => JenisSurat::all(['id', 'nama_surat', 'syarat']),
            
            // Mengambil riwayat pengajuan milik user yang sedang login
           'riwayatPengajuan' => PengajuanSurat::where('user_id', $user->id)
                        ->with('jenisSurat:id,nama_surat')
                        ->latest()
                        // Ambil kolom yang dibutuhkan, TERMASUK 'status' dan 'file_hasil'
                        ->get(['id', 'jenis_surat_id', 'user_id', 'status', 'file_hasil', 'created_at', 'lampiran']),
        ]);
    }

    /**
     * Menyimpan pengajuan surat baru yang dibuat oleh warga.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            // Validasi untuk file lampiran bisa ditambahkan di sini jika diperlukan
            // 'lampiran.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Snapshot data pemohon saat itu juga
        $dataPemohon = [
            'nama' => $user->name,
            'nik' => $user->nik,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
        ];

        PengajuanSurat::create([
            'user_id' => $user->id,
            'jenis_surat_id' => $request->jenis_surat_id,
            'data_pemohon' => $dataPemohon,
            'status' => 'pending',
            // Logika untuk upload file akan ditambahkan nanti
            'lampiran' => null, 
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan surat berhasil dikirim.');
    }

    /**
     * Membatalkan pengajuan surat yang statusnya masih 'pending'.
     */
    public function destroy(PengajuanSurat $pengajuanSurat)
    {
        // Pastikan user hanya bisa menghapus pengajuannya sendiri
        if ($pengajuanSurat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya pengajuan 'pending' yang bisa dibatalkan
        if ($pengajuanSurat->status !== 'pending') {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        $pengajuanSurat->delete();

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}
