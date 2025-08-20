<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str; // BARU: Tambahkan baris ini untuk mengimpor Str helper

class PengajuanSuratController extends Controller
{
    /**
     * Menampilkan halaman utama pengajuan surat untuk warga.
     */
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Pengajuan/Index', [
            'jenisSuratTersedia' => JenisSurat::all(['id', 'nama_surat', 'syarat']),
            'riwayatPengajuan' => PengajuanSurat::where('user_id', $user->id)
                                    ->with('jenisSurat:id,nama_surat')
                                    ->latest()
                                    ->get(['id', 'jenis_surat_id', 'user_id', 'status', 'file_hasil', 'created_at', 'lampiran']),
        ]);
    }

    /**
     * Menyimpan pengajuan surat baru yang dibuat oleh warga.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $jenisSurat = JenisSurat::findOrFail($request->jenis_surat_id);

        $validationRules = [
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
        ];

        if ($jenisSurat->syarat) {
            foreach ($jenisSurat->syarat as $syarat) {
                $slug = Str::slug($syarat, '_'); 
                $validationRules["lampiran.{$slug}"] = 'required|file|mimes:pdf|max:2048';
            }
        }

        $request->validate($validationRules);

        $dataPemohon = [
            'nama' => $user->name,
            'nik' => $user->nik,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
        ];

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $key => $file) {
                $path = $file->store('lampiran_warga', 'public');
                $lampiranPaths[$key] = $path; 
            }
        }

        PengajuanSurat::create([
            'user_id' => $user->id,
            'jenis_surat_id' => $request->jenis_surat_id,
            'data_pemohon' => $dataPemohon,
            'status' => 'pending',
            'lampiran' => $lampiranPaths, 
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan surat berhasil dikirim.');
    }

    /**
     * Membatalkan pengajuan surat yang statusnya masih 'pending'.
     */
    public function destroy(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($pengajuanSurat->status !== 'pending') {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }
        
        // Hapus file lampiran dari storage jika ada
        if ($pengajuanSurat->lampiran && is_array($pengajuanSurat->lampiran)) {
            foreach ($pengajuanSurat->lampiran as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $pengajuanSurat->delete();

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}