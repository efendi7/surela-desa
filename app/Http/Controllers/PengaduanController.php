<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PengaduanController extends Controller
{
    // === UNTUK WARGA ===
    public function index()
    {
        $user = Auth::user();
        
        // DIPERBAIKI: Load relasi admin dan tambahkan accessor untuk URL foto
        $allPengaduan = Pengaduan::where('user_id', $user->id)
            ->with(['admin:id,name'])
            ->latest()
            ->get()
            ->map(function ($pengaduan) {
                // Tambahkan URL foto bukti
                $pengaduan->foto_bukti_url = $pengaduan->foto_bukti 
                    ? Storage::url($pengaduan->foto_bukti) 
                    : null;
                
                // PENTING: Tambahkan URL foto proses untuk ditampilkan di modal warga
                $pengaduan->foto_proses_url = $pengaduan->foto_proses 
                    ? Storage::url($pengaduan->foto_proses) 
                    : null;
                
                return $pengaduan;
            });

        return Inertia::render('Pengaduan/Index', [
            'pengaduanAktif' => $allPengaduan->whereIn('status', ['Dikirim', 'Diterima', 'Diproses'])->values(),
            'riwayatPengaduan' => $allPengaduan->where('status', 'Selesai')->values(),
            'isProfileComplete' => $user->is_profile_complete,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'alamat' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'kategori' => 'nullable|string|max:100',
        ]);

        // Pastikan direktori ada
        Storage::makeDirectory('public/pengaduan/bukti');
        
        $pathBukti = $request->file('foto_bukti')->store('public/pengaduan/bukti');

        Pengaduan::create([
            'user_id' => Auth::id(),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'foto_bukti' => $pathBukti,
            'alamat' => $validated['alamat'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'kategori' => $validated['kategori'],
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dikirim.');
    }

    public function destroy(Pengaduan $pengaduan)
    {
        if ($pengaduan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus laporan ini.');
        }

        // Hapus foto bukti
        if (Storage::exists($pengaduan->foto_bukti)) {
            Storage::delete($pengaduan->foto_bukti);
        }

        // DIPERBAIKI: Hapus juga foto proses jika ada
        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        $pengaduan->delete();

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    // === UNTUK ADMIN ===
    public function adminIndex(Request $request)
    {
        $query = Pengaduan::with(['user:id,name,nik', 'admin:id,name']);

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
            ->withQueryString()
            ->through(function ($item) {
                // PENTING: Tambahkan URL untuk kedua foto
                $item->foto_bukti_url = $item->foto_bukti 
                    ? Storage::url($item->foto_bukti) 
                    : null;
                
                $item->foto_proses_url = $item->foto_proses 
                    ? Storage::url($item->foto_proses) 
                    : null;

                return $item;
            });

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
        ]);
    }

    public function updateDetails(Request $request, Pengaduan $pengaduan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dikirim,Diterima,Diproses,Selesai',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
            'keterangan_admin' => 'nullable|string',
            'estimasi_selesai' => 'nullable|date',
        ]);

        // Update admin_id dengan user yang sedang login
        $validated['admin_id'] = Auth::id();

        $pengaduan->update($validated);

        return redirect()->back()->with('success', 'Detail pengaduan berhasil diperbarui.');
    }

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
            'admin_id' => Auth::id(), // Set admin yang mengupload
        ]);

        return redirect()->back()->with('success', 'Foto penanganan berhasil diunggah.');
    }

    // Alias method untuk backward compatibility
    public function adminUploadProses(Request $request, Pengaduan $pengaduan)
    {
        return $this->uploadProses($request, $pengaduan);
    }

    // Method untuk admin show
    public function adminShow(Pengaduan $pengaduan)
    {
        $pengaduan->load(['user:id,name,nik', 'admin:id,name']);
        $pengaduan = $this->attachPhotoUrlsToSingle($pengaduan);
        
        return response()->json($pengaduan);
    }

    // Method untuk admin riwayat
    public function adminRiwayat(Request $request)
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
            ->withQueryString()
            ->through(function ($item) {
                return $this->attachPhotoUrlsToSingle($item);
            });

        return Inertia::render('Admin/Pengaduan/Riwayat', [
            'pengaduan' => $pengaduan,
            'filters' => $request->only(['kategori', 'search']),
        ]);
    }

    // Method untuk admin update status
    public function adminUpdateStatus(Request $request, Pengaduan $pengaduan)
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

    // Method untuk admin update details
    public function adminUpdateDetails(Request $request, Pengaduan $pengaduan)
    {
        return $this->updateDetails($request, $pengaduan);
    }

    // DIPERBAIKI: Method untuk menampilkan detail pengaduan (bisa diakses admin dan warga)
    public function show(Pengaduan $pengaduan)
    {
        // Cek autoritas: admin bisa lihat semua, warga hanya miliknya
        if (!Auth::user()->is_admin && $pengaduan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses pengaduan ini.');
        }

        // Load semua relasi yang dibutuhkan
        $pengaduan->load(['user:id,name,nik', 'admin:id,name']);
        
        // PENTING: Tambahkan URL foto untuk kedua foto
        $pengaduan->foto_bukti_url = $pengaduan->foto_bukti 
            ? Storage::url($pengaduan->foto_bukti) 
            : null;
        
        $pengaduan->foto_proses_url = $pengaduan->foto_proses 
            ? Storage::url($pengaduan->foto_proses) 
            : null;

        return response()->json($pengaduan);
    }

    // Method helper untuk mendapatkan pengaduan dengan foto URL
    private function attachPhotoUrls($pengaduan)
    {
        if ($pengaduan instanceof \Illuminate\Database\Eloquent\Collection) {
            return $pengaduan->map(function ($item) {
                return $this->attachPhotoUrlsToSingle($item);
            });
        }
        
        return $this->attachPhotoUrlsToSingle($pengaduan);
    }

    private function attachPhotoUrlsToSingle($pengaduan)
    {
        // Debug: Log file paths
        if ($pengaduan->foto_bukti) {
            \Log::info('Foto bukti path: ' . $pengaduan->foto_bukti);
            \Log::info('Foto bukti exists: ' . (Storage::exists($pengaduan->foto_bukti) ? 'Yes' : 'No'));
        }
        
        if ($pengaduan->foto_proses) {
            \Log::info('Foto proses path: ' . $pengaduan->foto_proses);
            \Log::info('Foto proses exists: ' . (Storage::exists($pengaduan->foto_proses) ? 'Yes' : 'No'));
        }
        
        $pengaduan->foto_bukti_url = $pengaduan->foto_bukti 
            ? Storage::url($pengaduan->foto_bukti) 
            : null;
        
        $pengaduan->foto_proses_url = $pengaduan->foto_proses 
            ? Storage::url($pengaduan->foto_proses) 
            : null;

        return $pengaduan;
    }

    // DIPERBAIKI: Method untuk API jika diperlukan
    public function apiIndex()
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            $pengaduan = Pengaduan::with(['user:id,name,nik', 'admin:id,name'])
                ->latest()
                ->get();
        } else {
            $pengaduan = Pengaduan::where('user_id', $user->id)
                ->with(['admin:id,name'])
                ->latest()
                ->get();
        }

        // Attach photo URLs
        $pengaduan = $this->attachPhotoUrls($pengaduan);

        return response()->json($pengaduan);
    }

    // Method untuk export/download foto
    public function downloadFoto(Pengaduan $pengaduan, $type)
    {
        // Validasi akses
        if (!Auth::user()->is_admin && $pengaduan->user_id !== Auth::id()) {
            abort(403);
        }

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
}