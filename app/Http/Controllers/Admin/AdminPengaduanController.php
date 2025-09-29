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

        // Perbaiki statistik - gunakan lowercase sesuai database
        $statistik = [
            'total' => Pengaduan::count(),
            'pending' => Pengaduan::where('status', 'pending')->count(),
            'diproses' => Pengaduan::where('status', 'diproses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
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
                          ->where('status', 'selesai'); // lowercase

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
     * Update status pengaduan dengan timeline
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai', // lowercase
        ]);

        // Ambil timeline yang sudah ada atau buat array kosong
        $timeline = $pengaduan->timeline ?? [];

        // Siapkan pesan timeline berdasarkan status
        $timelineMessage = $this->getTimelineMessage($validated['status']);

        // Tambah item baru ke timeline
        $timeline[] = [
            'status' => $validated['status'],
            'message' => $timelineMessage,
            'timestamp' => now()->toISOString(),
            'admin_name' => Auth::user()->name,
        ];

        $pengaduan->update([
            'status' => $validated['status'],
            'admin_id' => Auth::id(),
            'timeline' => $timeline,
        ]);

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    /**
     * Update detail lengkap pengaduan (status, prioritas, keterangan, dll) dengan timeline
     */
    public function updateDetails(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai', // lowercase
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat', // lowercase
            'keterangan_admin' => 'nullable|string',
            'estimasi_selesai' => 'nullable|date',
            'foto_proses' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update admin_id dengan user yang sedang login
        $validated['admin_id'] = Auth::id();

        // Handle file upload foto proses
        if ($request->hasFile('foto_proses')) {
            // Hapus foto lama jika ada
            if ($pengaduan->foto_proses && Storage::disk('public')->exists($pengaduan->foto_proses)) {
                Storage::disk('public')->delete($pengaduan->foto_proses);
            }
            // Simpan foto baru
            $validated['foto_proses'] = $request->file('foto_proses')->store('pengaduan/proses', 'public');
        }

        // Ambil timeline yang sudah ada atau buat array kosong
        $timeline = $pengaduan->timeline ?? [];

        // Siapkan pesan timeline berdasarkan status dan keterangan
        $timelineMessage = $this->getTimelineMessage($validated['status'], $validated['keterangan_admin'] ?? null);

        // Tambah item baru ke timeline
        $timeline[] = [
            'status' => $validated['status'],
            'message' => $timelineMessage,
            'timestamp' => now()->toISOString(),
            'admin_name' => Auth::user()->name,
        ];

        // Update timeline
        $validated['timeline'] = $timeline;

        $pengaduan->update($validated);

        // Load relasi untuk response
        $pengaduan->load(['user:id,name,nik', 'admin:id,name']);
        
        return redirect()->back()->with([
            'success' => 'Detail pengaduan berhasil diperbarui.',
            'updatedPengaduan' => $pengaduan
        ]);
    }

    /**
     * Upload foto proses penanganan dengan timeline
     */
    public function uploadProses(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'foto_proses' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($pengaduan->foto_proses && Storage::disk('public')->exists($pengaduan->foto_proses)) {
            Storage::disk('public')->delete($pengaduan->foto_proses);
        }

        // Upload foto baru
        $pathProses = $request->file('foto_proses')->store('pengaduan/proses', 'public');
        
        // Update timeline
        $timeline = $pengaduan->timeline ?? [];
        $timeline[] = [
            'status' => 'foto_uploaded',
            'message' => 'Foto penanganan telah diunggah oleh admin.',
            'timestamp' => now()->toISOString(),
            'admin_name' => Auth::user()->name,
        ];

        $pengaduan->update([
            'foto_proses' => $pathProses,
            'admin_id' => Auth::id(),
            'timeline' => $timeline,
        ]);

        return redirect()->back()->with('success', 'Foto penanganan berhasil diunggah.');
    }

    /**
     * Generate pesan timeline berdasarkan status
     */
    private function getTimelineMessage($status, $keterangan = null)
    {
        $messages = [
            'pending' => 'Laporan pengaduan diterima dan menunggu peninjauan.',
            'diproses' => 'Laporan pengaduan sedang dalam proses penanganan.',
            'selesai' => 'Laporan pengaduan telah selesai ditangani.',
        ];

        $message = $messages[$status] ?? 'Status pengaduan diperbarui.';

        if ($keterangan) {
            $message .= ' Catatan: ' . $keterangan;
        }

        return $message;
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

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Foto tidak ditemukan.');
        }

        // Return file langsung tanpa download
        return response()->file(Storage::disk('public')->path($filePath));
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

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($filePath, $fileName);
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
        if ($pengaduan->foto_bukti && Storage::disk('public')->exists($pengaduan->foto_bukti)) {
            Storage::disk('public')->delete($pengaduan->foto_bukti);
        }

        // Hapus juga foto proses jika ada
        if ($pengaduan->foto_proses && Storage::disk('public')->exists($pengaduan->foto_proses)) {
            Storage::disk('public')->delete($pengaduan->foto_proses);
        }

        $pengaduan->delete();

        return redirect()->back()->with('success', 'Pengaduan berhasil dihapus.');
    }
}