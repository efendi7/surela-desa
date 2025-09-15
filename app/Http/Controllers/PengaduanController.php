<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PengaduanController extends Controller
{
    // === UNTUK WARGA ===

    public function index()
    {
        $user = Auth::user();
        $allPengaduan = Pengaduan::where('user_id', $user->id)->latest()->get();

        // [PERUBAHAN] Logika pengecekan profil yang lama dihapus dari sini.

        return Inertia::render('Pengaduan/Index', [
            'pengaduanAktif' => $allPengaduan->whereIn('status', ['Dikirim', 'Diterima', 'Diproses'])->values(),
            'riwayatPengaduan' => $allPengaduan->where('status', 'Selesai')->values(),
            // [PERUBAHAN] Menggunakan accessor dari model User secara langsung.
            'isProfileComplete' => $user->is_profile_complete,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pathBukti = $request->file('foto_bukti')->store('public/pengaduan/bukti');

        Pengaduan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'foto_bukti' => $pathBukti,
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dikirim.');
    }
    
    public function destroy(Pengaduan $pengaduan)
    {
        if ($pengaduan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus laporan ini.');
        }

        if (Storage::exists($pengaduan->foto_bukti)) {
            Storage::delete($pengaduan->foto_bukti);
        }
        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        $pengaduan->delete();

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    // === UNTUK ADMIN ===

     public function adminIndex()
    {
        $pengaduanAktif = Pengaduan::with('user:id,name')
            ->whereIn('status', ['Dikirim', 'Diterima', 'Diproses'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Pengaduan/Index', [
            'pengaduan' => $pengaduanAktif,
            'pageTitle' => 'Pengaduan Aktif', // Menambah judul halaman
        ]);
    }

    /**
     * [PERUBAHAN] Method baru untuk menampilkan riwayat pengaduan yang sudah selesai.
     */
    public function adminRiwayat()
    {
        $pengaduanSelesai = Pengaduan::with('user:id,name')
            ->where('status', 'Selesai')
            ->latest()
            ->get();

        return Inertia::render('Admin/Pengaduan/Index', [
            'pengaduan' => $pengaduanSelesai,
            'pageTitle' => 'Riwayat Pengaduan', // Menambah judul halaman
        ]);
    }
    

    public function adminShow(Pengaduan $pengaduan)
    {
        return Inertia::render('Admin/Pengaduan/Show', [
            'pengaduan' => $pengaduan->load('user')
        ]);
    }

    public function adminUpdateStatus(Request $request, Pengaduan $pengaduan)
    {
        $request->validate(['status' => 'required|in:Diterima,Diproses,Selesai']);
        $pengaduan->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function adminUploadProses(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'foto_proses' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        $pathProses = $request->file('foto_proses')->store('public/pengaduan/proses');
        $pengaduan->update(['foto_proses' => $pathProses, 'status' => 'Diproses']);

        return redirect()->back()->with('success', 'Foto bukti proses berhasil diupload.');
    }
}