<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminPengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan untuk admin dengan filter
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['user:id,name,nik,profile_photo_path', 'admin:id,name']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        // Search berdasarkan judul atau deskripsi
        if ($request->filled('search')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $pengaduan = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik untuk dashboard
        $statistik = [
            'total' => Pengaduan::count(),
            'dikirim' => Pengaduan::where('status', 'Dikirim')->count(),
            'diterima' => Pengaduan::where('status', 'Diterima')->count(),
            'diproses' => Pengaduan::where('status', 'Diproses')->count(),
            'selesai' => Pengaduan::where('status', 'Selesai')->count(),
        ];

        return Inertia::render('Admin/Pengaduan/Index', [
            'pengaduan' => $pengaduan,
            'statistik' => $statistik,
            'filters' => $request->only(['status', 'prioritas', 'kategori', 'search']),
            'pageTitle' => 'Pengaduan Aktif',
        ]);
    }

    /**
     * Menampilkan riwayat pengaduan yang sudah selesai
     */
    public function riwayat(Request $request)
    {
        $query = Pengaduan::with(['user:id,name,nik', 'admin:id,name'])
                          ->where('status', 'Selesai');

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        // Search berdasarkan judul atau deskripsi
        if ($request->filled('search')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $pengaduan = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Pengaduan/Riwayat', [
            'pengaduan' => $pengaduan,
            'filters' => $request->only(['kategori', 'search']),
        ]);
    }

    /**
     * Menampilkan detail pengaduan untuk admin
     */
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['user:id,name,nik', 'admin:id,name']);
        
        return response()->json($pengaduan);
    }

    /**
     * Update status pengaduan
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dikirim,Diterima,Diproses,Selesai',
        ]);

        $pengaduan->update([
            'status' => $validated['status'],
            'admin_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    /**
     * Update detail lengkap pengaduan (status, prioritas, keterangan, dll)
     */
    public function updateDetails(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dikirim,Diterima,Diproses,Selesai',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
            'keterangan_admin' => 'nullable|string',
            'estimasi_selesai' => 'nullable|date',
            'foto_proses' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update admin_id dengan user yang sedang login
        $validated['admin_id'] = Auth::id();

        // Handle file upload foto proses
        if ($request->hasFile('foto_proses')) {
            // Hapus foto lama jika ada
            if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
                Storage::delete($pengaduan->foto_proses);
            }
            // Simpan foto baru
            $pathProses = $request->file('foto_proses')->store('public/pengaduan/proses');
            $validated['foto_proses'] = $pathProses;
        }

        $pengaduan->update($validated);

        // Load relasi untuk response
        $pengaduan->load(['user:id,name,nik', 'admin:id,name']);
        
        return redirect()->back()->with([
            'success' => 'Detail pengaduan berhasil diperbarui.',
            'updatedPengaduan' => $pengaduan
        ]);
    }

    /**
     * Upload foto proses penanganan
     */
    public function uploadProses(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'foto_proses' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pastikan direktori ada
        Storage::makeDirectory('public/pengaduan/proses');

        // Hapus foto lama jika ada
        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        // Upload foto baru
        $pathProses = $request->file('foto_proses')->store('public/pengaduan/proses');
        
        $pengaduan->update([
            'foto_proses' => $pathProses,
            'admin_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Foto penanganan berhasil diunggah.');
    }

    /**
     * Menampilkan foto bukti atau proses (admin bisa lihat semua)
     */
    public function viewFoto(Pengaduan $pengaduan, $type)
    {
        $filePath = null;

        if ($type === 'bukti' && $pengaduan->foto_bukti) {
            $filePath = $pengaduan->foto_bukti;
        } elseif ($type === 'proses' && $pengaduan->foto_proses) {
            $filePath = $pengaduan->foto_proses;
        }

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'Foto tidak ditemukan.');
        }

        // Return file langsung tanpa download
        return response()->file(Storage::path($filePath));
    }

    /**
     * Download foto untuk admin
     */
    public function downloadFoto(Pengaduan $pengaduan, $type)
    {
        $filePath = null;
        $fileName = '';

        if ($type === 'bukti' && $pengaduan->foto_bukti) {
            $filePath = $pengaduan->foto_bukti;
            $fileName = 'foto_bukti_' . $pengaduan->id . '_' . time() . '.jpg';
        } elseif ($type === 'proses' && $pengaduan->foto_proses) {
            $filePath = $pengaduan->foto_proses;
            $fileName = 'foto_proses_' . $pengaduan->id . '_' . time() . '.jpg';
        }

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::download($filePath, $fileName);
    }

    /**
     * API untuk mendapatkan semua pengaduan (admin)
     */
    public function apiIndex()
    {
        $pengaduan = Pengaduan::with(['user:id,name,nik', 'admin:id,name'])
            ->latest()
            ->get();

        return response()->json($pengaduan);
    }

    /**
     * Hapus pengaduan (hanya admin)
     */
    public function destroy(Pengaduan $pengaduan)
    {
        // Hapus foto bukti
        if (Storage::exists($pengaduan->foto_bukti)) {
            Storage::delete($pengaduan->foto_bukti);
        }

        // Hapus juga foto proses jika ada
        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        $pengaduan->delete();

        return redirect()->back()->with('success', 'Pengaduan berhasil dihapus.');
    }
}